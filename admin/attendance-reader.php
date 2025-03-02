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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!--<link rel="stylesheet" href="../css/dashboard.css">-->
    <?php include '../plugins.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>

</head>

<body>
    <div class="container">
        <div class="content" id="attendance">
            <div style="margin-top: 30px !important;">
                <a href="dashboard.php" class="back-icon" style="text-decoration: none; color: #2E3538;"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
            </div>
            <h1 style="font-size: 26px; margin-top: 25px;">QR Attendance Reader</h1>

            <div class="row">
                <div class="col-md-12">


                    <ul hidden>
                        <li v-for="(device, index) in available_devices" :key="device.deviceId">
                            <strong>Camera {{ index + 1 }}:</strong>
                            <p><strong>Label:</strong> </p>
                            <p><strong>Device ID:</strong> {{ device.deviceId }}</p>
                            <p><strong>Kind:</strong> {{ device.kind }}</p>
                        </li>
                    </ul>

                    <center>

                        <select class="form-control mb-2" @input="changeCamera()" v-model="selected_camera">
                            <option :value="device.deviceId" v-for="(device, index) in available_devices"
                                :key="device.deviceId">
                                {{ device.label }}
                            </option>
                        </select>

                    </center>

                    <center>
                        <video id="webcam" autoplay>

                        </video>

                        <img id="capturedImage" />

                        <button class="btn btn-dark w-100 mb-4" @click="captureCamera()">
                            Capture Image
                        </button>


                    </center>
                </div>
            </div>

        </div>




    </div>

    <script>
        new Vue({
            el: "#attendance",

            data() {
                return {
                    available_devices: [], // Ensure this is properly initialized
                    selected_camera: '',
                    qr_object: [],
                    qr_data: []
                };
            },

            mounted() {
                this.getAvailableCamera()
            },

            methods: {
                changeCamera() {
                    // this.getAvailableCamera()


                    var videoElement = document.getElementById('webcam');

                    // Stop any existing streams before starting a new one
                    if (videoElement.srcObject) {
                        const stream = videoElement.srcObject;
                        const tracks = stream.getTracks();
                        tracks.forEach(track => track.stop()); // Stop each track
                        videoElement.srcObject = null; // Clear the srcObject
                    }

                    const constraints = { video: { deviceId: this.selected_camera } };

                    navigator.mediaDevices.getUserMedia(constraints)
                        .then(function (stream) {
                            videoElement.srcObject = stream; // Set the new stream
                            this.stream_object = stream
                        })
                        .catch(function (error) {
                            console.error("Error accessing webcam: ", error);
                        });

                },
                async captureCamera() {


                    var videoElement = document.getElementById('webcam'); // Get the video element
                    var canvas = document.createElement('canvas'); // Create a canvas element to capture the image
                    var context = canvas.getContext('2d');
                    canvas.width = videoElement.videoWidth;
                    canvas.height = videoElement.videoHeight;
                    context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
                    var dataURL = canvas.toDataURL('image/png');
                    console.log(dataURL)
                    // Convert the base64 image to an ImageData object for jsQR
                    var img = new Image();
                    img.src = dataURL;

                    img.onload = () => {
                        var qrCanvas = document.createElement('canvas');
                        var qrContext = qrCanvas.getContext('2d');
                        qrCanvas.width = img.width;
                        qrCanvas.height = img.height;
                        qrContext.drawImage(img, 0, 0);

                        // Get ImageData from the canvas
                        var imageData = qrContext.getImageData(0, 0, qrCanvas.width, qrCanvas.height);

                        // Use jsQR to decode the QR code
                        var qrCode = jsQR(imageData.data, imageData.width, imageData.height);

                        if (qrCode) {
                            this.qr_data = []
                            const qr_code_content = JSON.parse(qrCode.data)
                            this.qr_data.push(
                                qr_code_content
                            )
                            console.log('has qr')
                        } else {
                            console.log('No QR code found');
                        }
                    };

                    console.log(this.qr_data)
                    console.log(this.qr_data.length)
                    if (this.qr_data.length > 0) {
                        const has_been_approved = await this.alreadyApproved(
                            this.qr_data[0].registration_id
                        )

                        if (has_been_approved === true) {
                            console.log('registration was approved, proceeding to next step.')

                            const has_attendance_already = await this.verifyExistingAttendance(
                                this.qr_data[0].registration_id
                            )

                            console.log(has_attendance_already)
                            console.log('is the attendance already')

                            if (has_attendance_already === false) {
                                const registration_info = await this.getRegistrationInfo(this.qr_data[0].registration_id)
                            }
                            else {
                                alert('Attendance was already logged before.')
                            }

                        } else {
                            alert('Registration not yet approved.')
                        }
                    }


                },

                async alreadyApproved(id) {
                    const url = '/bcp-ems/api/select.php?table=registration&id=' + String(id) + '&status=Approved'
                    const result = await axios.get(url)
                    if (result) {
                        return result.data.length > 0
                    }
                },

                async verifyExistingAttendance(id) {
                    const url = '/bcp-ems/api/select.php?table=attendance&registration_id=' + String(id)
                    const result = await axios.get(url)
                    if (result) {
                        return result.data.length > 0
                    }
                },

                async insertAttendance(id, program) {

                    const has_attendance_already = await this.verifyExistingAttendance(id)
                    console.log(has_attendance_already)
                    if (!has_attendance_already) {
                        const url = '/bcp-ems/api/insert.php?table=attendance'
                        const result = await axios.post(url, {
                            "registration_id": id,
                            "program": program
                        })

                        if (result) {
                            alert('Attendance logged.')
                        }

                    } else {
                        console.log('Has attendance already.')
                    }

                },

                async getRegistrationInfo(id) {
                    const url = '/bcp-ems/api/select.php?table=registration&id=' + String(id)
                    const result = await axios.get(url)
                    if (result) {
                        const info = result.data[0]
                        await this.insertAttendance(
                            id, info.program
                        )
                    }

                },
                // Get available cameras
                getAvailableCamera() {

                    navigator.mediaDevices.enumerateDevices()
                        .then((devices) => {
                            // Filter out video input devices (cameras)
                            const videoDevices = devices.filter(device => device.kind === 'videoinput');
                            this.available_devices = videoDevices; // Directly assigning to the array works here

                            this.$nextTick(() => {
                                console.log(this.available_devices);
                            });


                        })
                        .catch(function (error) {
                            console.error('Error enumerating devices: ', error);
                            alert("Error detecting devices.");
                        });
                }


                // // Get available cameras
                // getAvailableCamera() {

                //     navigator.mediaDevices.enumerateDevices()

                //         .then((devices) => {
                //             // Filter out video input devices (cameras)
                //             const videoDevices = devices.filter(device => device.kind === 'videoinput');
                //             this.available_devices = videoDevices; // Directly assigning to the array works here

                //             this.$nextTick(() => {
                //                 console.log(this.available_devices);
                //             });

                //             if (videoDevices.length > 0) {
                //                 const constraints = { video: { deviceId: videoDevices[0].deviceId } };
                //                 navigator.mediaDevices.getUserMedia(constraints)
                //                     .then(function (stream) {
                //                         var videoElement = document.getElementById('webcam');
                //                         videoElement.srcObject = stream;
                //                     })
                //                     .catch(function (error) {
                //                         console.error("Error accessing webcam: ", error);
                //                     });
                //             }

                //         })

                //         .catch(function (error) {
                //             console.error('Error enumerating devices: ', error);
                //         });
                // }
            },
        });
    </script>


    <script>
        // Log all media devices (video and audio)


    </script>


</body>

</html>