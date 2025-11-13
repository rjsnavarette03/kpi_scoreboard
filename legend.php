<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: /login.php');
    exit;
}
include('config/db.php');
include('includes/header.php');
include('includes/body-intro.php');
?>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">KPI Weight Distribution Table</h4>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-centered mb-0 align-middle table-hover table-nowrap">
                                    <thead class="table-light">
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
                                    </tbody><!-- end tbody -->
                                    <tfoot class="table-primary fw-bold">
                                        <tr>
                                            <td>Total</td>
                                            <td></td>
                                            <td>100%</td>
                                        </tr>
                                    </tfoot>
                                </table> <!-- end table -->
                            </div>
                            <div class="row mt-5">
                                <div class="col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table table-centered mb-0 align-middle table-hover table-nowrap">
                                            <thead class="table-success">
                                                <tr>
                                                    <th>Productivity (40%)</th>
                                                    <th style="width:75px;">Scoring</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>400 mins and up</td>
                                                    <td>100</td>
                                                </tr>
                                                <tr>
                                                    <td>370 - 399 mins</td>
                                                    <td>95</td>
                                                </tr>
                                                <tr>
                                                    <td>350 - 369 mins</td>
                                                    <td>90</td>
                                                </tr>
                                                <tr>
                                                    <td>320 - 349 mins</td>
                                                    <td>85</td>
                                                </tr>
                                                <tr>
                                                    <td>319 and below</td>
                                                    <td>80</td>
                                                </tr>
                                            </tbody><!-- end tbody -->
                                        </table> <!-- end table -->
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table table-centered mb-0 align-middle table-hover table-nowrap">
                                            <thead class="table-danger">
                                                <tr>
                                                    <th>Efficiency (20%)</th>
                                                    <th style="width:75px;">Scoring</th>
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
                                            </tbody><!-- end tbody -->
                                        </table> <!-- end table -->
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="table-responsive">
                                        <table class="table table-centered mb-0 align-middle table-hover table-nowrap">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th>Quality (20%)</th>
                                                    <th style="width:75px;">Scoring</th>
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
                                            </tbody><!-- end tbody -->
                                        </table> <!-- end table -->
                                    </div>
                                </div>
                                <div class="col-xl-4 mt-4">
                                    <div class="table-responsive">
                                        <table class="table table-centered mb-0 align-middle table-hover table-nowrap">
                                            <thead class="table-info">
                                                <tr>
                                                    <th>Attendance (10%)</th>
                                                    <th style="width:75px;">Scoring</th>
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
                                            </tbody><!-- end tbody -->
                                        </table> <!-- end table -->
                                    </div>
                                </div>
                                <div class="col-xl-4 mt-4">
                                    <div class="table-responsive">
                                        <table class="table table-centered mb-0 align-middle table-hover table-nowrap">
                                            <thead class="table-info">
                                                <tr>
                                                    <th>Tardiness (5%)</th>
                                                    <th style="width:75px;">Scoring</th>
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
                                            </tbody><!-- end tbody -->
                                        </table> <!-- end table -->
                                    </div>
                                </div>
                                <div class="col-xl-4 mt-4">
                                    <div class="table-responsive">
                                        <table class="table table-centered mb-0 align-middle table-hover table-nowrap">
                                            <thead class="table-info">
                                                <tr>
                                                    <th>Undertime (5%)</th>
                                                    <th style="width:75px;">Scoring</th>
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
                                            </tbody><!-- end tbody -->
                                        </table> <!-- end table -->
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card -->
                    </div><!-- end card -->
                </div>
            </div>
            <!-- end row -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Performance Rating Guide</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row d-flex flex-row justify-content-between g-0">
                                <div class="col-xl-2 bg-dark rounded d-flex flex-column justify-content-center align-items-center p-5">
                                    <p class="rating-title text-white mb-3">EX</p>
                                    <h4 class="rating-score text-white h3">100</h4>
                                    <p class="rating-description text-white mb-0">Exceptional</p>
                                </div>
                                <div class="col-xl-2 bg-dark rounded d-flex flex-column justify-content-center align-items-center p-5">
                                    <p class="rating-title text-white mb-3">EE</p>
                                    <h4 class="rating-score text-white h3">95 - 99.99</h4>
                                    <p class="rating-description text-white mb-0">Exceeds Expectations</p>
                                </div>
                                <div class="col-xl-2 bg-dark rounded d-flex flex-column justify-content-center align-items-center p-5">
                                    <p class="rating-title text-white mb-3">ME</p>
                                    <h4 class="rating-score text-white h3">90 - 94.99</h4>
                                    <p class="rating-description text-white mb-0">Meets Expectations</p>
                                </div>
                                <div class="col-xl-2 bg-dark rounded d-flex flex-column justify-content-center align-items-center p-5">
                                    <p class="rating-title text-white mb-3">NI</p>
                                    <h4 class="rating-score text-white h3">85 - 89.99</h4>
                                    <p class="rating-description text-white mb-0">Needs Improvement</p>
                                </div>
                                <div class="col-xl-2 bg-dark rounded d-flex flex-column justify-content-center align-items-center p-5">
                                    <p class="rating-title text-white mb-3">UN</p>
                                    <h4 class="rating-score text-white h3">&lt; 85</h4>
                                    <p class="rating-description text-white mb-0">Unsatisfactory</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
</div>
<!-- end main content-->
 <?php include('includes/footer.php'); ?>