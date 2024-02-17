<?php
  $page_title = 'All Product';
  require_once('includes/load.php');
  require 'vendor/autoload.php';
  use PhpOffice\PhpSpreadsheet\IOFactory;
  // Checkin What level user has permission to view this page
   page_require_level(2);
  $products = join_product_table();
?>

<?php
function displayTable($data)
  {
      // echo '<table border="1">';
      // echo '<tr>';
      // foreach ($data[0] as $key => $value) {
      //     echo '<th>' . $key . '</th>';
      // }
      // echo '</tr>';

      $row_count = 0;
      foreach ($data as $row) {
          $text=array();
          $columns_counts=0;
          // echo '<tr>';
          foreach ($row as $value) {
              // echo '<td>' . $value .'</td>';
              $text[$columns_counts]=$value;
              // echo '<td>' . $text[$counts] .'</td>';
              $columns_counts++;
          }
          // echo '</tr>';

          //-------------------------------------------------------
          if($row_count!=0){
            global $db;
            $insert_data = "'". $text[2] . "','" . $text[4]. "','". $text[5] . "','". $text[6] . "','" . $text[3] . "','ab'," ."'" . $text[8] . "'";
            $query  = "INSERT INTO products (";
            $query .="name,quantity,buy_price,sale_price,categorie_id,media_id,date";
            $query .=") VALUES (";
            $query .= $insert_data;
            $query .=")";
            $result = $db->query($query);
          }
          //-------------------------------------------------------
        $row_count++;
      }

      // echo '</table>';
      redirect('product.php');
  }

  // Check if the form is submitted
  if (isset($_POST['submit'])) {
      // Get the uploaded file
      $file = $_FILES['file']['tmp_name'];
    
      if (!empty($file)) {
          // Load the spreadsheet
      $spreadsheet = IOFactory::load($file);

      // Get the active sheet
      $sheet = $spreadsheet->getActiveSheet();

      // Define the columns
      $columns = ['ID', 'Photo', 'Product Title', 'Categories', 'In-Stock', 'Buying', 'Price', 'Selling Price', 'Product Added'];

      // Initialize an empty array to store data
      $data = [];

      // Iterate through the rows
      foreach ($sheet->getRowIterator() as $row) {
          $rowData = [];
          $cellIterator = $row->getCellIterator();
          $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
          foreach ($cellIterator as $cell) {
              $rowData[] = $cell->getValue();
          }
          $data[] = array_combine($columns, $rowData);
      }

      // Display the imported data
      displayTable($data);
      } else {
          echo "Please choose a file.";
      }
  }
?>


<?php include_once('layouts/header.php'); ?>
  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
         <div class="pull-right">
           <a href="demo excel file.xlsx" class="btn btn-primary">download demo</a>
           <a href="add_product.php" class="btn btn-primary">Add New</a>
         </div>
		
          <!-- <form method="post" enctype="multipart/form-data">
            <label class="btn btn-primary" for="file">Choose File...</label>
            <input type="file" name="file" id="file" accept=".xlsx, .xls" style="display:none;" onchange="displayFileName()">
            <input class="btn btn-primary" type="submit" name="submit" value="Import">
          </form>
          <div id="fileNameDisplay"></div>

          <script>
            function displayFileName() {
                var fileInput = document.getElementById('file');
                var fileNameDisplay = document.getElementById('fileNameDisplay');

                if (fileInput.files.length > 0) {
                    var fileName = fileInput.files[0].name;
                    fileNameDisplay.innerHTML = 'Selected File: ' + fileName;
                } else {
                    fileNameDisplay.innerHTML = '';
                }
            }
          </script> -->

<!-- ************************************************************************************************************************* -->

          <form method="post" enctype="multipart/form-data" id="uploadForm">
            <label class="btn btn-primary" for="file">Select File</label>
            <input type="file" name="file" id="file" accept=".xlsx, .xls" style="display:none;" onchange="enableImportButton()">
            <input class="btn btn-primary" type="submit" name="submit" value="Import" id="importButton" disabled onclick="submitForm()">
        </form>

        <div id="fileNameDisplay"></div>

        <script>
            function enableImportButton() {
                var fileInput = document.getElementById('file');
                var importButton = document.getElementById('importButton');

                if (fileInput.files.length > 0) {
                    importButton.removeAttribute('disabled');
                } else {
                    importButton.setAttribute('disabled', 'disabled');
                }

                // Optional: Update the selected file name display
                displayFileName();
            }

            function submitForm() {
                var formSubmitted = document.getElementsByName('formSubmitted')[0].value;
                if (formSubmitted === '1') {
                    var formData = new FormData($('#uploadForm')[0]);

                    $.ajax({
                        type: 'POST',
                        url: 'process.php', // Replace with the actual path to your PHP file
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            $('#output').html(response);
                        },
                        error: function () {
                            alert('Error processing the file.');
                        }
                    });
                }
            }

            function displayFileName() {
                var fileInput = document.getElementById('file');
                var fileNameDisplay = document.getElementById('fileNameDisplay');

                if (fileInput.files.length > 0) {
                    var fileName = fileInput.files[0].name;
                    fileNameDisplay.innerHTML = 'Selected File: ' + fileName;
                } else {
                    fileNameDisplay.innerHTML = '';
                }
            }
        </script>
<!-- ************************************************************************************************************************* -->

        <div class="panel-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th> Photo</th>
                <th> Product Title </th>
                <th class="text-center" style="width: 10%;"> Categories </th>
                <th class="text-center" style="width: 10%;"> In-Stock </th>
                <th class="text-center" style="width: 10%;"> Buying Price </th>
                <th class="text-center" style="width: 10%;"> Selling Price </th>
                <th class="text-center" style="width: 10%;"> Product Added </th>
                <th class="text-center" style="width: 100px;"> Actions </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product):?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td>
                  <?php if($product['media_id'] === '0'): ?>
                    <img class="img-avatar img-circle" src="uploads/products/no_image.png" alt="">
                  <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['image']; ?>" alt="">
                <?php endif; ?>
                </td>
                <td> <?php echo remove_junk($product['name']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['categorie']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['quantity']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['buy_price']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['sale_price']); ?></td>
                <td class="text-center"> <?php echo read_date($product['date']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-info btn-xs"  title="Edit" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-danger btn-xs"  title="Delete" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>
              </tr>
             <?php endforeach; ?>
          </tabel>
        </div>
      </div>
    </div>
  </div>
  <?php include_once('layouts/footer.php'); ?>
