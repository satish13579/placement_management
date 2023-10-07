<?php
include 'auth.php';

include 'menu.php';
?>
<link rel="stylesheet" href="index.css">
<div class="main-container">
    <div style="margin: auto;
				margin-top:20px;
    	padding: 10px;" class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-6">
                    <h2><b>Past Placements </b></h2>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="placements_table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class='align-middle text-center'>SL NO</th>
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
                    $result = $conn->prepare("SELECT * FROM placements WHERE roll_no = ?");
                    $result->execute(array($_SESSION['id']));

                    $arrs = $result->fetchAll();
                    $i = 1;
                    foreach ($arrs as $row) {
                    ?>
                        <tr id="<?php echo $row['roll_no']; ?>">
                            <td class='align-middle text-center'><?php echo $i; ?></td>
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
                                            <a target="_blank" href="<?php echo $offerrow['offer_letter']; ?>">Offer Letter Pending to Approve <i class="fa-solid fa-clock"></i></a>
                                        <?php
                                        } else if ($offerrow['approved'] == 1) {
                                        ?>
                                            <i class='fa-solid fa-circle-check'></i> <span class='placed'>Offer Letter Accepted</span>
                                        <?php
                                        } else if ($offerrow['approved'] == 0) {
                                        ?>
                                            <a href="#addOfferLetter" data-bs-toggle="modal" data-bs-target="#addOfferLetter" class="upload_offer_modal" data-id="<?php echo $row['id']; ?>">Offer Letter Rejected, Upload Again <i class="fa-solid fa-upload"></i></a>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <a href="#addOfferLetter" data-bs-toggle="modal" data-bs-target="#addOfferLetter" class="upload_offer_modal" data-id="<?php echo $row['id']; ?>">Upload Offer Letter <i class="fa-solid fa-upload" style="color: #fff;"></i></a>
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

<div id="addOfferLetter" class="modal fade" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adds_form">
                    <div class="form-group">
                        <input id="btn-adds-sub" type='submit' style="display:none">
                        <input type="hidden" value="offer_upload" name="type">
                        <input type="hidden" name="placement_id" id="placement_id">
                        <label>Upload Offer Letter</label><br>
                        <input id='inputffile' class='form-control' type='file' accept='.pdf,.jpg,.png,.jpeg,.docx,.doc' name='uploadedFile' required>
                        <span id='filefsize'></span>
                    </div>
                </form>
            </div>
        <div class="modal-footer">
            <input type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" value="Cancel">
            <button type="button" class="btn btn-primary" id="btn-adds">Publish</button>
        </div>
        </div>
    </div>
</div>
</div>
<script>
    $(document).ready(function() {
        $('#placements_table').DataTable({
            columnDefs: [{
                orderable: false,
                targets: 6
            }],
            order: [
                [0, "asc"]
            ]
        });
    });

    $('.upload_offer_modal').on('click', function(e) {
        $('#placement_id').val($(this).attr('data-id'));
    });

    $('#btn-adds').on('click', function(e) {
        var form = document.querySelector("#adds_form");
        if (!form.checkValidity()) {
            $('#btn-adds-sub').click();
        } else {
            $.ajax({
                data: new FormData(document.getElementById('adds_form')),
                url: 'student.php',
                type: "POST",
                processData: false,
                contentType: false,
                success: function(data) {
                    var dr = JSON.parse(data);
                    if (dr.statusCode == 200) {
                        alert("Offer Letter Uploaded Successfully.!!");
                        location.reload();
                    } else if (dr.statusCode == 400) {
                        alert(dr.msg);
                    }
                }
            });
        }
    });

    $(document).on('change', '#inputffile', function(e) {
        var inputfile = document.getElementById('inputffile');
        var filess = inputfile.files;
        var size = Math.round(filess[0].size / 1024);

        if (size <= 1024 * 20) {
            $('#filefsize').html(Math.round((size / 1024) * 100) / 100 + ' MB');
            return;
        } else {
            alert("The Selected File Size is " + Math.round((size / 1024) * 100) / 100 + ' MB\n' + "File Size Should Not Be Greater Than 20 MB.!!");
            inputfile.value = '';
        }
    });
</script>