<?php
require_once __DIR__ . '/components/config-header.php';
include("components/header.php");
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
    <h1>Dashboard</h1>
    <ol class="breadcrumb mb-0">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-4 col-xs-6">

        <div class="small-box bg-aqua">
          <div class="inner">
            <h3>
              <?php echo $getdata->getRecordCount("tbl_enquiry"); ?>
            </h3>
            <p>Enquiries</p>
          </div>
          <div class="icon">
            <i class="fa fa-question-circle" aria-hidden="true"></i>
          </div>
          <a href="enquiry.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <!-- ./col -->
      <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
          <div class="inner">
            <h3><?php echo $getdata->getRecordCount("tbl_product"); ?></h3>
            <p>Products</p>
          </div>

          <div class="icon">
            <i class="fa fa-archive"></i>
          </div>
          <a href="product.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <!-- ./col -->
      <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">

          <div class="inner">
            <h3><?php echo $getdata->getRecordCount("tbl_product_category"); ?></h3>
            <p>Product Categories</p>
          </div>
          <div class="icon">
            <i class="fa fa-tags"></i>
          </div>
          <a href="product-category.php" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
    </div>
    <!-- /.row -->
    <!-- Main row -->
    <div class="row">
      <!-- Left col -->
      <section class="col-lg-12 connectedSortable">
        <!-- TO DO List -->
        <div class="box box-primary">
          <div class="box-header">
            <i class="ion ion-clipboard"></i>
            <h3 class="box-title">Recent Inquiry</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <ul class="todo-list">
              <?php
              $qry = "SELECT name, created_at FROM tbl_enquiry ORDER BY created_at DESC LIMIT 10";
              $result = $getdata->FetchQuery($qry);

              if ($result->num_rows === 0) {
              ?>
                <li>No inquiry in database.</li>
                <?php
              } else {
                while ($resinq = $result->fetch_assoc()) {
                ?>
                  <li>
                    <span class="handle">
                      <i class="fa fa-ellipsis-v"></i>
                      <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <span class="text">Enquiry by <?php echo $resinq['name']; ?> on <?php echo $resinq['created_at']; ?></span>
                    <div class="tools">
                      <i class="fa fa-eye" onclick="window.location.href='enquiry.php'"></i>
                    </div>
                  </li>
              <?php
                }
              }
              ?>
            </ul>

          </div>
          <!-- /.box-body -->

        </div>
        <!-- /.box -->
      </section>
      <!-- /.Left col -->
    </div>
    <!-- /.row (main row) -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php include("components/footer.php"); ?>
</body>

</html>
