<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: /login.php');
    exit;
}
include('config/db.php');
include('includes/header.php');
?>

<body>
    <?php include('includes/navbar.php'); ?>
    <div class="container-fluid">
        <div class="row min-100vh">
            <?php include('includes/sidebar.php'); ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 p-md-5 neumorph-container">
                <h2 class="mb-4">KPI Weight Distribution Table</h2>
                <div class="row">
                    <div class="col-xl-12 col-md-12 mb-4">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>KPI</th>
                                    <th>Definitions</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Productivity</td>
                                    <td>Total productive minutes logged per day/week/month</td>
                                    <td>40%</td>
                                </tr>
                                <tr>
                                    <td>Efficiency</td>
                                    <td>Consistency in meeting weekly productivity targets, Timely patient touch base and Completion of documentation within expected timeframes</td>
                                    <td>20%</td>
                                </tr>
                                <tr>
                                    <td>Quality</td>
                                    <td>Accuracy of work, Level of client/patient engagement and satisfaction</td>
                                    <td>20%</td>
                                </tr>
                                <tr>
                                    <td>Schedule Adherence</td>
                                    <td>Overall schedule adherence rate</td>
                                    <td>20%</td>
                                </tr>
                            </tbody>
                            <tfoot class="table-primary fw-bold">
                                <tr>
                                    <td>Total</td>
                                    <td></td>
                                    <td>100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4 col-md-12 mb-4">
                        <h3>Productivity</h3>
                        <table class="table">
                            <thead class="table-success">
                                <tr>
                                    <th>Metric</th>
                                    <th>Scoring</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Productivity Description One</td>
                                    <td>100</td>
                                </tr>
                                <tr>
                                    <td>Productivity Description Two</td>
                                    <td>95</td>
                                </tr>
                                <tr>
                                    <td>Productivity Description Three</td>
                                    <td>90</td>
                                </tr>
                                <tr>
                                    <td>Productivity Description Four</td>
                                    <td>85</td>
                                </tr>
                                <tr>
                                    <td>Productivity Description Five</td>
                                    <td>80</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xl-4 col-md-12 mb-4">
                        <h3>Efficiency</h3>
                        <table class="table">
                            <thead class="table-danger">
                                <tr>
                                    <th>Metric</th>
                                    <th>Scoring</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Meeting weekly productivity target</td>
                                    <td>100</td>
                                </tr>
                                <tr>
                                    <td>Missing 1 week of productivity target</td>
                                    <td>95</td>
                                </tr>
                                <tr>
                                    <td>Missing 2 weeks of productivity target</td>
                                    <td>90</td>
                                </tr>
                                <tr>
                                    <td>Missing 3 weeks of productivity target</td>
                                    <td>85</td>
                                </tr>
                                <tr>
                                    <td>Not meeting weekly priductivity target</td>
                                    <td>80</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xl-4 col-md-12 mb-4">
                        <h3>Quality</h3>
                        <table class="table">
                            <thead class="table-warning">
                                <tr>
                                    <th>Metric</th>
                                    <th>Scoring</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>100% accuracy and zero patient/client escalation</td>
                                    <td>100</td>
                                </tr>
                                <tr>
                                    <td>1 call outs and zero patient/client escalation</td>
                                    <td>95</td>
                                </tr>
                                <tr>
                                    <td>2 call outs and zero patient/client escalation</td>
                                    <td>90</td>
                                </tr>
                                <tr>
                                    <td>3 call outs or 1 patient/client escalation</td>
                                    <td>85</td>
                                </tr>
                                <tr>
                                    <td>4 call outs or 2 or more patient/client escalation</td>
                                    <td>80</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xl-12 col-md-12 mb-4">
                        <h3>Schedule Adherence</h3>
                        <div class="row">
                            <div class="col-xl-4 col-md-4 mb-4">
                                <table class="table">
                                    <thead class="table-info">
                                        <tr>
                                            <th>Attendance</th>
                                            <th>Scoring</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>0 Absence</td>
                                            <td>100</td>
                                        </tr>
                                        <tr>
                                            <td>1 Absence/Emergency Leave/Unplanned Absence</td>
                                            <td>90</td>
                                        </tr>
                                        <tr>
                                            <td>>1 Absence/Emergency Leave/Unplanned Absence</td>
                                            <td>80</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xl-4 col-md-4 mb-4">
                                <table class="table">
                                    <thead class="table-info">
                                        <tr>
                                            <th>Tardiness</th>
                                            <th>Scoring</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>0 Tardiness</td>
                                            <td>100</td>
                                        </tr>
                                        <tr>
                                            <td>1-15 accumulated minutes/ 1 instance of tardiness</td>
                                            <td>95</td>
                                        </tr>
                                        <tr>
                                            <td>16-30 accumulated minutes/ 2 instances of tardiness</td>
                                            <td>90</td>
                                        </tr>
                                        <tr>
                                            <td>>30 accumulated minutes/ 3 instances of tardiness</td>
                                            <td>80</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xl-4 col-md-4 mb-4">
                                <table class="table">
                                    <thead class="table-info">
                                        <tr>
                                            <th>Undertime</th>
                                            <th>Scoring</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>0 Undertime</td>
                                            <td>100</td>
                                        </tr>
                                        <tr>
                                            <td>1 Undertime</td>
                                            <td>90</td>
                                        </tr>
                                        <tr>
                                            <td>>1 Undertime</td>
                                            <td>80</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>