<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Manage Users</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <script src="https://code.jquery.com/jquery-3.6.3.js"
        integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.css">

    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.js">
    </script>

    <style>
    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    </style>

    @include('include.header')
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            @include('admin.sidebar')
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">


                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Users /</span> Manage</h4>
                        <div class="card">
                            <div style="display: flex;">
                                <h5 class="card-header">Manage Users</h5>
                            </div>
                            <div class="table table-responsive">
                                <table id="table_id" class="display">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Package</th>
                                            <th>Expiry Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @php($i = 1)
                                        @foreach ($users as $singledata)
                                        <tr>
                                            <td>{{ $i++; }}</td>
                                            <td>{{ $singledata['name'] }} </td>
                                            <td>{{ $singledata['email'] }}</td>
                                            <td>{{ $singledata['phone'] }}</td>
                                            <td>{{ $singledata['plan_name'] }}</td>
                                            <td>{{ date('d M, Y', strtotime($singledata['subscription']['end_date'])) }}</td>
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox" id="status-{{ $singledata['id'] }}"
                                                        onclick="updateUserStatus(this)"
                                                        data-key="{{ $singledata['id'] }}"
                                                        data-value="{{ $singledata['status'] == 1 ? '0' : '1' }}"
                                                        {{ $singledata['status'] == 1 ? 'checked' : '' }}>
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    </div>

    <style>
    .docDiv {
        column-gap: 15px;
        row-gap: 12px;
        align-items: center;
        justify-content: center;
    }

    .docDiv .docCol {
        padding: 10px 10px;
        border: 1px solid #c3c3c3;
        display: flex;
        justify-content: space-between;
    }
    </style>



    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src={{asset("assets/vendor/libs/popper/popper.js")}}></script>
    <script src={{asset("assets/vendor/js/bootstrap.js")}}></script>
    <script src={{asset("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js")}}></script>
    <script src={{asset("assets/vendor/js/menu.js")}}></script>
    <!-- endbuild -->
    <!-- Vendors JS -->
    <script src={{asset("assets/vendor/libs/apex-charts/apexcharts.js")}}></script>
    <!-- Main JS -->
    <script src={{asset("assets/js/main.js")}}></script>
    <!-- Page JS -->
    <script src={{asset("assets/js/dashboards-analytics.js")}}></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>


    <script>
    function closeModal() {
        $('.newRow').remove(); //remove all add on documents
        $('#editUser')[0].reset(); // remove all data in inputs
        $('#basicModal').modal('hide'); //hide the modal 
    }

    function closeModal() {
        $('.docCol').remove(); //remove all add on documents
        $('#docModal').modal('hide'); //hide the modal 
    }

    function show_Toaster(message, type) {
        var success = "#00b09b, #96c93d";
        var error = "#a7202d, #ff4044";
        var ColorCode = type == "success" ? success : error;
        return Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "bottom", // top or bottom
            position: "center", // left, center or right
            stopOnFocus: true, // Prevents dismissing of toast on hover
            style: {
                background: `linear-gradient(to right, ${ColorCode})`,
            },
        }).showToast();
    }

    $(document).ready(function() {
        $('#table_id').DataTable();
    });

    function deleteUser(key) {
        if (confirm('Are you sure?')) {
            axios.post(`${url}/admin/deleteUser`, {
                key,
            }).then(function(response) {
                // handle success
                show_Toaster(response.data.message, response.data.type)
                if (response.data.type === 'success') {
                    setTimeout(() => {
                        window.location.href = `${url}/admin/allUsers`;
                    }, 500);
                }
            }).catch(function(err) {
                show_Toaster(err.response.data.message, 'error')
            })
        }
    }

    function updateUserStatus(e) {
        value = $(e).attr('data-value');
        id = $(e).attr('id');
        if (confirm('Are you sure, you want to ' + (value == 1 ? 'Activate' : 'Deactivate'))) {
            key = $(e).attr('data-key');

            axios.post(`${url}/admin/updateUserStatus`, {
                key,
                value,
            }).then(function(response) {
                // handle success
                show_Toaster(response.data.message, response.data.type)
                if (response.data.type === 'success') {

                    $(e).attr('data-value', (value == 1 ? 0 : 1));
                    document.getElementById(id).checked = value == 1 ? true : false;
                    // setTimeout(() => {
                    //     window.location.href = `${url}/admin/allUsers`;
                    // }, 500);
                }
            }).catch(function(err) {
                show_Toaster(err.response.data.message, 'error')
            })

        } else {
            document.getElementById(id).checked = value == 1 ? false : true;
            return false;
        }
    }

    function createDocumentDiv(e) {
        var lastkey = $(e).val();
        lastkey++;
        var html = `
        
        <div class="row newRow docrow-${lastkey}">
        <hr>
            <div class="mb-3 col-md-5">
                <label for="email" class="form-label">Document Name</label>
                <input class="form-control" type="text" id="documents[${lastkey}][name]"
                    name="documents[${lastkey}][name]" placeholder="Enter Document Name" />
            </div>
            <div class="mb-3 col-md-5">
                <label for="firstName" class="form-label">Select Documents</label>
                <input class="form-control" type="file" id="documents[${lastkey}][file]"
                    name="documents[${lastkey}][file]" />
            </div>
            <div class="mb-3 col-md-1">
                <button type="button" class="btn btn-danger" onclick="deleteRow('docrow-${lastkey}')"><i class="menu-icon tf-icons bx bxs-trash"></i></button>
            </div>

        </div>
        `;

        $(e).val(lastkey)
        $('.docrow-0').append(html);
    }


    function deleteRow(className) {
        $(`.${className}`).remove();
    }


    function getDocument(client_id) {

        $('.docCol').remove();
        axios.get(`${url}/getDocument/${client_id}`).then(function(response) {
            // handle success
            show_Toaster(response.data.message, response.data.type)
            if (response.data.type === 'success') {
                var html = response.data.html;
                $('#docdiv').append(html);
                $('#docModal').modal('show');
            }
        }).catch(function(err) {
            show_Toaster(err.response.data.message, 'error')
        })
    }

    function deleteDoc(e) {

        var client_id = $(e).attr('data-client-id');
        var id = $(e).attr('data-id');
        axios.get(`${url}/deleteDocument/${client_id}/${id}`).then(function(response) {
            // handle success
            show_Toaster(response.data.message, response.data.type)
            if (response.data.type === 'success') {
                setTimeout(() => {
                    window.location.href = `${url}/admin/allUsers`;
                }, 1000);
            }
        }).catch(function(err) {
            show_Toaster(err.response.data.message, 'error')
        })
    }
    </script>
</body>

</html>