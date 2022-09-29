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
  if(empty($_POST['name']) || empty($_POST['description']) || empty($_POST['category']) || empty($_POST['quantity']) || empty($_POST['price'])){
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

    }elseif(is_numeric($_POST['quantity']) != 1 || is_numeric($_POST['price']) != 1){
    if(is_numeric($_POST['quantity']) != 1){
        $qtyError = "Quantity should be integer";
    }

    if(is_numeric($_POST['price']) != 1){
        $priceError = "Price should be integer";
    }
    }else{
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        if($_FILES['image']['name'] != null){
            $file = 'images/' . $_FILES['image']['name'];
            $imageType = pathinfo($file,PATHINFO_EXTENSION);
            if($imageType != 'jpg' && $imageType != 'jpeg' && $imageType != 'png'){
                echo "<script>alert('Image type should be of jpg,jpeg or png');</script>";
            }else{
            $image = $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'],$file);
            $stmt = $pdo->prepare("UPDATE products SET name=:name,description=:description,category_id=:category,quantity=:quantity,price=:price,image=:image WHERE id=:id");
            $result = $stmt->execute([
                ':id' => $id,
              ':name' => $name,
              ':description' => $description,
              ':category' => $category,
              ':quantity' => $quantity,
              ':price' => $price,
              ':image' => $image,
            ]);
            if($result){
              echo "<script>alert('Product Updated');window.location.href='index.php'</script>";
            }
            }
        }else{
            $stmt = $pdo->prepare("UPDATE products SET name=:name,description=:description,category_id=:category,quantity=:quantity,price=:price WHERE id=:id");
            $result = $stmt->execute([
                ':id' => $id,
              ':name' => $name,
              ':description' => $description,
              ':category' => $category,
              ':quantity' => $quantity,
              ':price' => $price,
            ]);
            if($result){
              echo "<script>alert('Product Updated');window.location.href='index.php'</script>";
            }
        }
  
    
    
  }
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id=" . $_GET['id']);
$stmt->execute();
$result = $stmt->fetchAll();       

?>


<?php include('header.php'); ?>
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <form class="" action="" method="post" enctype="multipart/form-data">
                  <input name="_token" type="hidden" value="<?php echo $_SESSION['_token']; ?>">
                  <input type="hidden" name="id" value="<?php echo $result[0]['id'] ?>">
                  <div class="form-group">
                    <label for="">Name</label><p style="color:red"><?php echo empty($nameError) ? '' : '*'.$nameError; ?></p>
                    <input type="text" class="form-control" name="name" value="<?php echo escape($result[0]['name']) ?>">
                  </div>
                  <div class="form-group">
                    <label for="">Description</label><p style="color:red"><?php echo empty($descError) ? '' : '*'.$descError; ?></p>
                    <textarea class="form-control" name="description" rows="8" cols="30"><?php echo escape($result[0]['description']) ?></textarea>
                  </div>
                  <div class="form-group">
                    <?php 
                         $catstmt = $pdo->prepare("SELECT * FROM categories");
                         $catstmt->execute();
                         $catresult = $catstmt->fetchAll();
                    ?>
                    <label for="">Category</label><p style="color:red"><?php echo empty($catError) ? '' : '*'.$catError; ?></p>
                    <select name="category" id="">
                        <option value="">Choose category</option>
                        <?php 
                       

                        foreach($catresult as $value){
                            ?>
                            <?php if($value['id'] == $result[0]['category_id']) : ?>
                             <option value="<?php echo $value['id'] ?>" selected><?php echo $value['name'] ?></option>
                             <?php else : ?>
                            <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
                            <?php endif ?>
                        <?php
                        }
                        ?>
                       
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="">Quantity</label><p style="color:red"><?php echo empty($qtyError) ? '' : '*'.$qtyError; ?></p>
                    <input type="text" class="form-control" name="quantity" value="<?php echo escape($result[0]['quantity']) ?>">
                  </div>
                  <div class="form-group">
                    <label for="">Price</label><p style="color:red"><?php echo empty($priceError) ? '' : '*'.$priceError; ?></p>
                    <input type="text" class="form-control" name="price" value="<?php echo escape($result[0]['price']) ?>">
                  </div>
                  <div class="form-group">
                    <img src="images/<?php echo $result[0]['image'] ?>" alt="">
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
  <?php include('footer.html')?>
