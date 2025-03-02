<?php
require_once '../connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$full_name = $_SESSION['full_name'] ?? 'User';
$email = $_SESSION['email'] ?? 'example@bcp.edu.ph';
$role = $_SESSION['role'] ?? 'Unknown Role';

// Get the event_id from the URL
if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
    die("Invalid event.");
}
$event_id = $_GET['event_id'];

// Fetch user details
$sql = "SELECT program, section, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $program = $user['program'];
    $section = $user['section'];
    $email = $user['email'];
} else {
    die("User data not found.");
}

// Fetch event details
$sql_event = "SELECT title FROM events WHERE id = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("i", $event_id);
$stmt_event->execute();
$result_event = $stmt_event->get_result();

if ($result_event->num_rows > 0) {
    $event = $result_event->fetch_assoc();
    $event_title = $event['title'];
} else {
    die("Event not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $event_id = $_POST['event_id'];

    // Check if the user is already registered for this event
    $check_sql = "SELECT id FROM registration WHERE user_id = ? AND event_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("ii", $user_id, $event_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        die("You are already registered for this event.");
    }

    // Insert the registration into the database
    $insert_sql = "INSERT INTO registration (user_id, event_id) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("ii", $user_id, $event_id);

    if ($stmt_insert->execute()) {
        echo "Registration successful! <a href='events.php'>Go back to events</a>";
    } else {
        echo "Error registering: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Register</title>
    <?php include '../plugins.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <!-- jQuery (Required for Bootstrap 4 JavaScript) -->


</head>
<body>
    <div class="container">
        <div class="content" id="registration">

            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Save Your QR Code</h5>

                        </div>
                        <div class="modal-body">
                            <center>
                                <div id="qrcode">
                                </div>
                            </center>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px !important;">
                <a href="events.php" class="back-icon" style="text-decoration: none; color: #2E3538;"><i class="fa fa-arrow-left"></i> Back to Events</a>
            </div>
            <h1 style="font-size: 26px; margin-top: 25px;">Event Registration</h1>

            <div>

                <div>
                    <label for="full_name">Full Name</label>
                    <input class="form-control" type="text" id="full_name" name="full_name"
                        value="<?= htmlspecialchars($full_name) ?>" readonly>
                </div>
                <div>
                    <label for="program">Program</label>
                    <input class="form-control" type="text" id="program" name="program"
                        value="<?= htmlspecialchars($program) ?>">
                </div>
                <div>
                    <label for="section">Section</label>
                    <input class="form-control" type="text" id="section" name="section"
                        value="<?= htmlspecialchars($section) ?>" readonly>
                </div>

                <div>
                    <label for="email">Email</label>
                    <input class="form-control" type="text" id="email" name="email"
                        value="<?= htmlspecialchars($email) ?>" placeholder="@gmail.com ">
                </div>

                <button :disabled="button_is_enabled" type="submit" @click="setData()" class="btn btn-dark mt-2 w-100">
                    Submit Registration
                </button>

            </div>
        </div>
    </div>

    <script src="scripts/burger.js"></script>

    <script>

        new Vue({
            el: "#registration",
            data: {

                "program": '',
                "registration_record": '',
                "qrtext": "",
                "data": [],
                "qr_object": null,
                "button_is_enabled": false,
                "name": "Doc Calim"
            },
            computed: {},
            watch: {},

            mounted() {
                this.qr_object = new QRCode("qrcode")
                this.isRegistered()
            },


            methods: {

                async getEventInfo() {
                    const url = '/bcp-ems/api/select.php?table=events&id=' + '<?php echo $event_id ?>'
                    const result = await axios.get(url)
                    return result
                },

                async isRegistered() {
                    const event_id = '<?php echo $event_id ?>'
                    const email = '<?php echo $email ?>'
                    const url = '/bcp-ems/api/select.php?table=registration&event_id=' + event_id + '&email=' + email
                    const is_registered = await axios.get(url)

                    if (is_registered) {
                        this.button_is_enabled = is_registered.data.length > 0
                        console.log(this.button_is_enabled)
                        return is_registered.data.length > 0
                    }

                },

                async registerData() {
                    const is_registered = await this.isRegistered()

                    if (is_registered === true) {
                        console.log('You are registered to this event!')
                        this.button_is_enabled = is_registered.data.length > 0
                        return
                    }

                    let qr_image = ''
                    const url = '/bcp-ems/api/insert.php?table=registration';

                    // Send the request to the API
                    const result = await axios.post(url, this.registration_record);

                    // Clear the previous QR code
                    this.qr_object.clear();

                    // Generate the new QR code with the received result.id
                    this.qr_object.makeCode(JSON.stringify({
                        "registration_id": result.data.id
                    }));

                    // Wait until the QR code image is available
                    await new Promise(resolve => {
                        setTimeout(() => {
                            const imgElement = this.qr_object._el.querySelector("img");

                            if (imgElement && imgElement.src) {
                                resolve(imgElement.src);
                            } else {
                                resolve(null);  // Retry if the image source is not available yet
                            }
                        }, 500); // 500ms delay (adjust as needed)
                    })
                        .then((base64Image) => {
                            if (base64Image) {
                                // Create a new Image object for the QR code image
                                const img = new Image();
                                img.src = base64Image;

                                img.onload = () => {
                                    // Create a canvas to manipulate the image
                                    const canvas = document.createElement('canvas');
                                    const ctx = canvas.getContext('2d');

                                    // Set the canvas size to 256x256 (standard QR size)
                                    canvas.width = 256 * 3;
                                    canvas.height = 256 * 4;

                                    // Draw a white background first
                                    ctx.fillStyle = 'white';
                                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                                    // Calculate the position to center the QR code inside the square
                                    const x = (canvas.width - img.width) / 2;
                                    const y = (canvas.height - img.height) / 2;
                                    console.log(x)
                                    console.log(y)
                                    // Draw the QR code image centered
                                    ctx.drawImage(img, x, y);


                                    qr_image = canvas.toDataURL(); // This is your manipulated image in base64 format

                                };
                            }
                        });

                    this.qrtext = '';

                    const content_data = JSON.parse(this.registration_record);
                    const event_info = await this.getEventInfo();
                    if (event_info) {
                        console.log(event_info.data[0].title);
                    }

                    let message = `Greetings, ${content_data.full_name}. You are now registered to ${event_info.data[0].title} event, at ${event_info.data[0].venue} from ${event_info.data[0].start_time} until ${event_info.data[0].end_time}. Please present your QR code sent from the attachment at the said venue during the event. This will serve as your attendance.`;

                    // Send the email with the attachment (QR image)
                    const email_response = await axios.post('/bcp-ems/api/sendmail.php/', {
                        'message': message,
                        'attachment': String(qr_image),  // Pass the manipulated base64 image
                        'recepient': content_data.email
                    });

                    $('#exampleModal').modal("toggle");
                },


                async setData() {

                    this.registration_record = JSON.stringify({
                        "full_name": '<?php echo $full_name ?>',
                        "program": '<?php echo $program ?>',
                        "email": '<?php echo $email ?>',
                        "section": '<?php echo $section ?>',
                        "event_id": '<?php echo $event_id ?>'
                    })

                    await this.registerData()

                }

            },

        });

    </script>

</body>

</html>