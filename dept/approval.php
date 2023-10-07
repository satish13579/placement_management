<?php
include 'auth.php';

include 'menu.php';

?>
<div class="main-container">
    <div style="margin: auto;
				margin-top:20px;
    	padding: 10px;" class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-6">
                    <h2>Past <b>Placements </b></h2>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="placements_table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class='align-middle text-center'>SL NO</th>
                        <th class='align-middle text-center'>ROLL NO</th>
                        <th class='align-middle text-center'>JOB ROLE</th>
                        <th class='align-middle text-center'>COMPANY</th>
                        <th class='align-middle text-center'>PACKAGE</th>
                        <th class='align-middle text-center'>DATE</th>
                        <th class='align-middle text-center'>STATUS</th>
                        <th class='align-middle text-center'>IS ACCEPTED</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $result = $conn->prepare("SELECT * FROM placements WHERE roll_no in (select roll_no FROM students WHERE dept_id=?)");

                    $result->execute(array($_SESSION['id']));

                    $arrs = $result->fetchAll();
                    $i = 1;
                    foreach ($arrs as $row) {
                    ?>
                        <tr id="<?php echo $row['roll_no']; ?>">
                            <td class='align-middle text-center'><?php echo $i; ?></td>
                            <td class='align-middle text-center'><?php echo $row['roll_no']; ?></td>
                            <td class='align-middle text-center'><?php echo $row["job_role"]; ?></td>
                            <td class='align-middle text-center'><?php echo $row["company"]; ?></td>
                            <td class='align-middle text-center'><?php echo $row["package"]; ?></td>
                            <td class='align-middle text-center' style="white-space:pre;"><?php echo $row["date"]; ?></td>
                            <td class='align-middle text-center'><?php if ($row['job_status'] == 1) {
                                                                        echo "<i class='fa-solid fa-circle-check'></i> <span class='placed'>Placed</span>";
                                                                    } else {
                                                                        echo "<i class='fa-solid fa-circle-xmark'></i> <span class='rejected'>Rejected</span>";
                                                                    } ?></td>
                            <td class='align-middle text-center'>
                                <?php if ($row['job_status'] == 1) {
                                    $placement_id = $row['id'];
                                    $offerq = $conn->prepare("SELECT * FROM `offer_letters` WHERE `placement_id`=?");
                                    $offerq->execute(array($placement_id));
                                    if ($offerq->rowCount() > 0) {
                                        $offerrow = $offerq->fetch();
                                        if ($offerrow['approved'] == -1) {
                                ?>
                                            <a target="_blank" href="<?php echo $offerrow['offer_letter']; ?>">Link to Offer Letter <i class="fa-solid fa-up-right-from-square"></i></a>
                                            <br><a data-bs-toggle="modal" data-bs-target="#ApproveOffer" data-id="<?php echo $row['id']; ?>" class="btn btn-success approve">Approve</a><a data-bs-toggle="modal" data-bs-target="#RejectOffer" data-id="<?php echo $row['id']; ?>" class="btn btn-danger reject">Reject</a>
                                        <?php
                                        } else if ($offerrow['approved'] == 1) {
                                        ?>
                                            <i class='fa-solid fa-circle-check'></i> <span class='placed'>Approved </span>
                                        <?php
                                        } else if ($offerrow['approved'] == 0) {
                                        ?>
                                            <i class="fa-solid fa-circle-xmark"></i> <span class="rejected">Rejected</span>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        Offer Letter Need to be Updated <i class="fa-solid fa-clock"></i>
                                <?php
                                    }
                                } else {
                                    echo "-";
                                } ?>

                            </td>

                        </tr>
                    <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<div id="ApproveOffer" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Approve Offer Letter</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="approve_form">
                    <input type="hidden" id="placement_id_a" name="placement_id" class="form-control">
                    <input type="hidden" name="type" value="approve">
                </form>

                <p>Are you sure you want to Approve this Offer Letter?</p>
                <p class="text-warning"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" value="Cancel">
                <button type="button" class="btn btn-success" id="approve-btn">Approve</button>
            </div>
        </div>
    </div>
</div>

<div id="RejectOffer" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reject Offer Letter</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reject_form">
                    <input type="hidden" id="placement_id_r" name="placement_id" class="form-control">
                    <input type="hidden" name="type" value="reject">
                </form>
                <p>Are you sure you want to Reject this Offer Letter?</p>
                <p class="text-warning"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" value="Cancel">
                <button type="button" class="btn btn-success" id="reject-btn">Reject</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('.approve').on('click', function(e) {
        $('#placement_id_a').val($(this).attr('data-id'));
    });
    $('.reject').on('click', function(e) {
        $('#placement_id_r').val($(this).attr('data-id'));
    });

    $('#approve-btn').on('click', function(e) {
        var data = $("#approve_form").serialize();
        $.ajax({
            url: "app.php",
            type: "POST",
            cache: false,
            data: data,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if (dataResult.statusCode == 200) {
                    alert('Offer Letter Approved successfully !');
                    window.location.reload();
                } else if (dataResult.statusCode == 400) {
                    alert(dataResult.msg);
                }
            }
        });
    });

    $('#reject-btn').on('click', function(e) {
        var data = $("#reject_form").serialize();
        $.ajax({
            url: "app.php",
            type: "POST",
            cache: false,
            data: data,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if (dataResult.statusCode == 200) {
                    alert('Offer Letter Approved successfully !');
                    window.location.reload();
                } else if (dataResult.statusCode == 400) {
                    alert(dataResult.msg);
                }
            }
        });
    });

    $(document).ready( function () {
    $('#placements_table').DataTable({});
        } );
</script>