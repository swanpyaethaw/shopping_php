<?php
session_start();
require 'config/config.php';
require 'config/common.php';

if (empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
  header('Location: login.php');
}
if ($_SESSION['role'] != 1) {
  header('Location: login.php');
}

if($_POST){
  if(empty($_POST['name']) || empty($_POST['description']) || empty($_POST['category']) || empty($_POST['quantity']) || empty($_POST['price']) || empty($_FILES['image'])){
    if(empty($_POST['name'])){
      $nameError = "Category name is required";
    }

    if(empty($_POST['description'])){
        $descError = "Description is required";
      }

    if(empty($_POST['category'])){
      $catError = "Category  is required";
    }

    if(empty($_POST['quantity'])){
        $qtyError = "Quantity is required";
      }elseif(is_numeric($_POST['quantity']) != 1 ){
        $qtyError = "Quantity should be integer";
      }

    if(empty($_POST['price'])){
      $priceError = "Price is required";
    }elseif(is_numeric($_POST['price']) != 1){
        $priceError = "Price should be integer";
    }

    if(empty($_FILES['image']['name'])){
        $imgError = "Image is required";
      }

    }elseif(is_numeric($_POST['quantity']) != 1 || is_numeric($_POST['price']) != 1){
    if(is_numeric($_POST['quantity']) != 1){
        $qtyError = "Quantity should be integer";
    }

    if(is_numeric($_POST['price']) != 1){
        $priceError = "Price should be integer";
    }
    }else{
    $file = 'images/' . $_FILES['image']['name'];
    $imageType = pathinfo($file,PATHINFO_EXTENSION);
    if($imageType != 'jpg' && $imageType != 'jpeg' && $imageType != 'png'){
        echo "<script>alert('Image type should be of jpg,jpeg or png');</script>";
    }else{
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'],$file);
    $stmt = $pdo->prepare("INSERT INTO products (name,description,category_id,quantity,price,image) VALUES (:name,:description,:category,:quantity,:price,:image)");
    $result = $stmt->execute([
      ':name' => $name,
      ':description' => $description,
      ':category' => $category,
      ':quantity' => $quantity,
      ':price' => $price,
      ':image' => $image,
    ]);
    if($result){
      echo "<script>alert('Product Added');window.location.href='index.php'</script>";
    }
    }
    
    
  }
}

        

?>


<?php include('header.php'); ?>
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <form class="" action="product_add.php" method="post" enctype="multipart/form-data">
                  <input name="_token" type="hidden" value="<?php echo $_SESSION['_token']; ?>">
                  <div class="form-group">
                    <label for="">Name</label><p style="color:red"><?php echo empty($nameError) ? '' : '*'.$nameError; ?></p>
                    <input type="text" class="form-control" name="name" value="">
                  </div>
                  <div class="form-group">
                    <label for="">Description</label><p style="color:red"><?php echo empty($descError) ? '' : '*'.$descError; ?></p>
                    <textarea class="form-control" name="description" rows="8" cols="80"></textarea>
                  </div>
                  <div class="form-group">
                    <label for="">Category</label><p style="color:red"><?php echo empty($catError) ? '' : '*'.$catError; ?></p>
                    <select name="category" id="">
                        <option value="">Choose category</option>
                        <?php 
                        $catstmt = $pdo->prepare("SELECT * FROM categories");
                        $catstmt->execute();
                        $catresult = $catstmt->fetchAll();

                        foreach($catresult as $value){
                            ?>
                             <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
                        <?php
                        }
                        ?>
                       
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="">Quantity</label><p style="color:red"><?php echo empty($qtyError) ? '' : '*'.$qtyError; ?></p>
                    <input type="number" class="form-control" name="quantity" value="">
                  </div>
                  <div class="form-group">
                    <label for="">Price</label><p style="color:red"><?php echo empty($priceError) ? '' : '*'.$priceError; ?></p>
                    <input type="number" class="form-control" name="price" value="">
                  </div>
                  <div class="form-group">
                    <label for="">Image</label><p style="color:red"><?php echo empty($imgError) ? '' : '*'.$imgError; ?></p>
                    <input type="file" class="" name="image" value="">
                  </div>
                  <div class="form-group">
                    <input type="submit" class="btn btn-success" name="" value="SUBMIT">
                    <a href="index.php" class="btn btn-warning">Back</a>
                  </div>
                </form>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  <?php include('footer.html')