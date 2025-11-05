<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

include 'components/save_send.php';

// Initialize search variables - these will be used to pre-fill the form
$search_location = '';
$search_type = '';
$search_offer = '';
$search_bhk = '';
$search_min = '';
$search_max = '';
$search_status = '';
$search_furnished = '';

// Check if coming from home page search
if(isset($_POST['h_search'])){
   // Get the location value from home page
   $h_location = isset($_POST['h_location']) ? $_POST['h_location'] : '';
   $h_location = filter_var($h_location, FILTER_SANITIZE_STRING);

   // Store location for pre-filling the form
   $search_location = $h_location;

   // Build query with location only
   $query = "SELECT * FROM `property` WHERE 1=1";

   if(!empty($h_location)){
      $query .= " AND (LOWER(address) LIKE LOWER('%{$h_location}%') OR LOWER(property_name) LIKE LOWER('%{$h_location}%'))";
   }

   $query .= " ORDER BY date DESC";
   $select_properties = $conn->prepare($query);
   $select_properties->execute();

}elseif(isset($_POST['filter_search'])){

   $location = isset($_POST['location']) ? $_POST['location'] : '';
   $location = filter_var($location, FILTER_SANITIZE_STRING);
   $type = isset($_POST['type']) ? $_POST['type'] : '';
   $type = filter_var($type, FILTER_SANITIZE_STRING);
   $offer = isset($_POST['offer']) ? $_POST['offer'] : '';
   $offer = filter_var($offer, FILTER_SANITIZE_STRING);
   $bhk = isset($_POST['bhk']) ? $_POST['bhk'] : '';
   $bhk = filter_var($bhk, FILTER_SANITIZE_STRING);
   $min = isset($_POST['min']) ? $_POST['min'] : '';
   $min = filter_var($min, FILTER_SANITIZE_STRING);
   $max = isset($_POST['max']) ? $_POST['max'] : '';
   $max = filter_var($max, FILTER_SANITIZE_STRING);
   $status = isset($_POST['status']) ? $_POST['status'] : '';
   $status = filter_var($status, FILTER_SANITIZE_STRING);
   $furnished = isset($_POST['furnished']) ? $_POST['furnished'] : '';
   $furnished = filter_var($furnished, FILTER_SANITIZE_STRING);

   // Store values for pre-filling the form
   $search_location = $location;
   $search_type = $type;
   $search_offer = $offer;
   $search_bhk = $bhk;
   $search_min = $min;
   $search_max = $max;
   $search_status = $status;
   $search_furnished = $furnished;

   // Build query with optional parameters - CASE INSENSITIVE
   $query = "SELECT * FROM `property` WHERE 1=1";

   if(!empty($location)){
      $query .= " AND (LOWER(address) LIKE LOWER('%{$location}%') OR LOWER(property_name) LIKE LOWER('%{$location}%'))";
   }
   if(!empty($type)){
      $query .= " AND LOWER(type) LIKE LOWER('%{$type}%')";
   }
   if(!empty($offer)){
      $query .= " AND LOWER(offer) LIKE LOWER('%{$offer}%')";
   }
   if(!empty($bhk)){
      $query .= " AND bhk = '{$bhk}'";
   }
   if(!empty($status)){
      $query .= " AND LOWER(status) LIKE LOWER('%{$status}%')";
   }
   if(!empty($furnished)){
      $query .= " AND LOWER(furnished) LIKE LOWER('%{$furnished}%')";
   }
   if(!empty($min) && !empty($max)){
      $query .= " AND price BETWEEN $min AND $max";
   }elseif(!empty($min)){
      $query .= " AND price >= $min";
   }elseif(!empty($max)){
      $query .= " AND price <= $max";
   }

   $query .= " ORDER BY date DESC";
   $select_properties = $conn->prepare($query);
   $select_properties->execute();

}else{
   $select_properties = $conn->prepare("SELECT * FROM `property` ORDER BY date DESC LIMIT 6");
   $select_properties->execute();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search Page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- search filter section starts  -->

<section class="filters" style="padding-bottom: 0;">

   <form action="" method="post">
      <div id="close-filter"><i class="fas fa-times"></i></div>
      <h3>filter your search</h3>

         <div class="flex">
            <div class="box">
               <p>enter location</p>
               <input type="text" name="location" maxlength="50" placeholder="enter city name" class="input" value="<?= htmlspecialchars($search_location); ?>">
            </div>
            <div class="box">
               <p>offer type</p>
               <select name="offer" class="input">
                  <option value="">any</option>
                  <option value="sale" <?= ($search_offer == 'sale') ? 'selected' : ''; ?>>sale</option>
                  <option value="resale" <?= ($search_offer == 'resale') ? 'selected' : ''; ?>>resale</option>
                  <option value="rent" <?= ($search_offer == 'rent') ? 'selected' : ''; ?>>rent</option>
               </select>
            </div>
            <div class="box">
               <p>property type</p>
               <select name="type" class="input">
                  <option value="">any</option>
                  <option value="flat" <?= ($search_type == 'flat') ? 'selected' : ''; ?>>flat</option>
                  <option value="house" <?= ($search_type == 'house') ? 'selected' : ''; ?>>house</option>
                  <option value="shop" <?= ($search_type == 'shop') ? 'selected' : ''; ?>>shop</option>
               </select>
            </div>
            <div class="box">
               <p>how many BHK</p>
               <select name="bhk" class="input">
                  <option value="">any</option>
                  <option value="1" <?= ($search_bhk == '1') ? 'selected' : ''; ?>>1 BHK</option>
                  <option value="2" <?= ($search_bhk == '2') ? 'selected' : ''; ?>>2 BHK</option>
                  <option value="3" <?= ($search_bhk == '3') ? 'selected' : ''; ?>>3 BHK</option>
                  <option value="4" <?= ($search_bhk == '4') ? 'selected' : ''; ?>>4 BHK</option>
                  <option value="5" <?= ($search_bhk == '5') ? 'selected' : ''; ?>>5 BHK</option>
                  <option value="6" <?= ($search_bhk == '6') ? 'selected' : ''; ?>>6 BHK</option>
                  <option value="7" <?= ($search_bhk == '7') ? 'selected' : ''; ?>>7 BHK</option>
                  <option value="8" <?= ($search_bhk == '8') ? 'selected' : ''; ?>>8 BHK</option>
                  <option value="9" <?= ($search_bhk == '9') ? 'selected' : ''; ?>>9 BHK</option>
               </select>
            </div>
            <div class="box">
               <p>minimum budget</p>
               <select name="min" class="input">
                  <option value="">any</option>
                  <option value="5000" <?= ($search_min == '5000') ? 'selected' : ''; ?>>5k</option>
                  <option value="10000" <?= ($search_min == '10000') ? 'selected' : ''; ?>>10k</option>
                  <option value="15000" <?= ($search_min == '15000') ? 'selected' : ''; ?>>15k</option>
                  <option value="20000" <?= ($search_min == '20000') ? 'selected' : ''; ?>>20k</option>
                  <option value="30000" <?= ($search_min == '30000') ? 'selected' : ''; ?>>30k</option>
                  <option value="40000" <?= ($search_min == '40000') ? 'selected' : ''; ?>>40k</option>
                  <option value="50000" <?= ($search_min == '50000') ? 'selected' : ''; ?>>50k</option>
                  <option value="100000" <?= ($search_min == '100000') ? 'selected' : ''; ?>>1 lac</option>
                  <option value="500000" <?= ($search_min == '500000') ? 'selected' : ''; ?>>5 lac</option>
                  <option value="1000000" <?= ($search_min == '1000000') ? 'selected' : ''; ?>>10 lac</option>
                  <option value="2000000" <?= ($search_min == '2000000') ? 'selected' : ''; ?>>20 lac</option>
                  <option value="3000000" <?= ($search_min == '3000000') ? 'selected' : ''; ?>>30 lac</option>
                  <option value="4000000" <?= ($search_min == '4000000') ? 'selected' : ''; ?>>40 lac</option>
                  <option value="5000000" <?= ($search_min == '5000000') ? 'selected' : ''; ?>>50 lac</option>
                  <option value="6000000" <?= ($search_min == '6000000') ? 'selected' : ''; ?>>60 lac</option>
                  <option value="7000000" <?= ($search_min == '7000000') ? 'selected' : ''; ?>>70 lac</option>
                  <option value="8000000" <?= ($search_min == '8000000') ? 'selected' : ''; ?>>80 lac</option>
                  <option value="9000000" <?= ($search_min == '9000000') ? 'selected' : ''; ?>>90 lac</option>
                  <option value="10000000" <?= ($search_min == '10000000') ? 'selected' : ''; ?>>1 Cr</option>
                  <option value="20000000" <?= ($search_min == '20000000') ? 'selected' : ''; ?>>2 Cr</option>
                  <option value="30000000" <?= ($search_min == '30000000') ? 'selected' : ''; ?>>3 Cr</option>
                  <option value="40000000" <?= ($search_min == '40000000') ? 'selected' : ''; ?>>4 Cr</option>
                  <option value="50000000" <?= ($search_min == '50000000') ? 'selected' : ''; ?>>5 Cr</option>
                  <option value="60000000" <?= ($search_min == '60000000') ? 'selected' : ''; ?>>6 Cr</option>
                  <option value="70000000" <?= ($search_min == '70000000') ? 'selected' : ''; ?>>7 Cr</option>
                  <option value="80000000" <?= ($search_min == '80000000') ? 'selected' : ''; ?>>8 Cr</option>
                  <option value="90000000" <?= ($search_min == '90000000') ? 'selected' : ''; ?>>9 Cr</option>
                  <option value="100000000" <?= ($search_min == '100000000') ? 'selected' : ''; ?>>10 Cr</option>
                  <option value="150000000" <?= ($search_min == '150000000') ? 'selected' : ''; ?>>15 Cr</option>
                  <option value="200000000" <?= ($search_min == '200000000') ? 'selected' : ''; ?>>20 Cr</option>
               </select>
            </div>
            <div class="box">
               <p>maximum budget</p>
               <select name="max" class="input">
                  <option value="">any</option>
                  <option value="5000" <?= ($search_max == '5000') ? 'selected' : ''; ?>>5k</option>
                  <option value="10000" <?= ($search_max == '10000') ? 'selected' : ''; ?>>10k</option>
                  <option value="15000" <?= ($search_max == '15000') ? 'selected' : ''; ?>>15k</option>
                  <option value="20000" <?= ($search_max == '20000') ? 'selected' : ''; ?>>20k</option>
                  <option value="30000" <?= ($search_max == '30000') ? 'selected' : ''; ?>>30k</option>
                  <option value="40000" <?= ($search_max == '40000') ? 'selected' : ''; ?>>40k</option>
                  <option value="50000" <?= ($search_max == '50000') ? 'selected' : ''; ?>>50k</option>
                  <option value="100000" <?= ($search_max == '100000') ? 'selected' : ''; ?>>1 lac</option>
                  <option value="500000" <?= ($search_max == '500000') ? 'selected' : ''; ?>>5 lac</option>
                  <option value="1000000" <?= ($search_max == '1000000') ? 'selected' : ''; ?>>10 lac</option>
                  <option value="2000000" <?= ($search_max == '2000000') ? 'selected' : ''; ?>>20 lac</option>
                  <option value="3000000" <?= ($search_max == '3000000') ? 'selected' : ''; ?>>30 lac</option>
                  <option value="4000000" <?= ($search_max == '4000000') ? 'selected' : ''; ?>>40 lac</option>
                  <option value="5000000" <?= ($search_max == '5000000') ? 'selected' : ''; ?>>50 lac</option>
                  <option value="6000000" <?= ($search_max == '6000000') ? 'selected' : ''; ?>>60 lac</option>
                  <option value="7000000" <?= ($search_max == '7000000') ? 'selected' : ''; ?>>70 lac</option>
                  <option value="8000000" <?= ($search_max == '8000000') ? 'selected' : ''; ?>>80 lac</option>
                  <option value="9000000" <?= ($search_max == '9000000') ? 'selected' : ''; ?>>90 lac</option>
                  <option value="10000000" <?= ($search_max == '10000000') ? 'selected' : ''; ?>>1 Cr</option>
                  <option value="20000000" <?= ($search_max == '20000000') ? 'selected' : ''; ?>>2 Cr</option>
                  <option value="30000000" <?= ($search_max == '30000000') ? 'selected' : ''; ?>>3 Cr</option>
                  <option value="40000000" <?= ($search_max == '40000000') ? 'selected' : ''; ?>>4 Cr</option>
                  <option value="50000000" <?= ($search_max == '50000000') ? 'selected' : ''; ?>>5 Cr</option>
                  <option value="60000000" <?= ($search_max == '60000000') ? 'selected' : ''; ?>>6 Cr</option>
                  <option value="70000000" <?= ($search_max == '70000000') ? 'selected' : ''; ?>>7 Cr</option>
                  <option value="80000000" <?= ($search_max == '80000000') ? 'selected' : ''; ?>>8 Cr</option>
                  <option value="90000000" <?= ($search_max == '90000000') ? 'selected' : ''; ?>>9 Cr</option>
                  <option value="100000000" <?= ($search_max == '100000000') ? 'selected' : ''; ?>>10 Cr</option>
                  <option value="150000000" <?= ($search_max == '150000000') ? 'selected' : ''; ?>>15 Cr</option>
                  <option value="200000000" <?= ($search_max == '200000000') ? 'selected' : ''; ?>>20 Cr</option>
               </select>
            </div>
            <div class="box">
               <p>status</p>
               <select name="status" class="input">
                  <option value="">any</option>
                  <option value="ready to move" <?= ($search_status == 'ready to move') ? 'selected' : ''; ?>>ready to move</option>
                  <option value="under construction" <?= ($search_status == 'under construction') ? 'selected' : ''; ?>>under construction</option>
               </select>
            </div>
            <div class="box">
               <p>furnished</p>
               <select name="furnished" class="input">
                  <option value="">any</option>
                  <option value="unfurnished" <?= ($search_furnished == 'unfurnished') ? 'selected' : ''; ?>>unfurnished</option>
                  <option value="furnished" <?= ($search_furnished == 'furnished') ? 'selected' : ''; ?>>furnished</option>
                  <option value="semi-furnished" <?= ($search_furnished == 'semi-furnished') ? 'selected' : ''; ?>>semi-furnished</option>
               </select>
            </div>
         </div>
         <input type="submit" value="search property" name="filter_search" class="btn">
   </form>

</section>

<!-- search filter section ends -->

<div id="filter-btn" class="fas fa-filter"></div>

<!-- listings section starts  -->

<section class="listings">

   <?php
      if(isset($_POST['h_search']) or isset($_POST['filter_search'])){
         echo '<h1 class="heading">search results</h1>';
      }else{
         echo '<h1 class="heading">latest listings</h1>';
      }
   ?>

   <div class="box-container">
      <?php
         $total_images = 0;
         if($select_properties->rowCount() > 0){
            while($fetch_property = $select_properties->fetch(PDO::FETCH_ASSOC)){
            $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_user->execute([$fetch_property['user_id']]);
            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

            if(!empty($fetch_property['image_02'])){
               $image_coutn_02 = 1;
            }else{
               $image_coutn_02 = 0;
            }
            if(!empty($fetch_property['image_03'])){
               $image_coutn_03 = 1;
            }else{
               $image_coutn_03 = 0;
            }
            if(!empty($fetch_property['image_04'])){
               $image_coutn_04 = 1;
            }else{
               $image_coutn_04 = 0;
            }
            if(!empty($fetch_property['image_05'])){
               $image_coutn_05 = 1;
            }else{
               $image_coutn_05 = 0;
            }

            $total_images = (1 + $image_coutn_02 + $image_coutn_03 + $image_coutn_04 + $image_coutn_05);

            $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE property_id = ? and user_id = ?");
            $select_saved->execute([$fetch_property['id'], $user_id]);

      ?>
      <form action="" method="POST">
         <div class="box">
            <input type="hidden" name="property_id" value="<?= $fetch_property['id']; ?>">
            <?php
               if($select_saved->rowCount() > 0){
            ?>
            <button type="submit" name="save" class="save"><i class="fas fa-heart"></i><span>saved</span></button>
            <?php
               }else{
            ?>
            <button type="submit" name="save" class="save"><i class="far fa-heart"></i><span>save</span></button>
            <?php
               }
            ?>
            <div class="thumb">
               <p class="total-images"><i class="far fa-image"></i><span><?= $total_images; ?></span></p>
               <img src="uploaded_files/<?= $fetch_property['image_01']; ?>" alt="">
            </div>
            <div class="admin">
               <h3><?= substr($fetch_user['name'], 0, 1); ?></h3>
               <div>
                  <p><?= $fetch_user['name']; ?></p>
                  <span><?= $fetch_property['date']; ?></span>
               </div>
            </div>
         </div>
         <div class="box">
            <div class="price"><i class="fas fa-dollar-sign"></i><span><?= $fetch_property['price']; ?></span></div>
            <h3 class="name"><?= $fetch_property['property_name']; ?></h3>
            <p class="location"><i class="fas fa-map-marker-alt"></i><span><?= $fetch_property['address']; ?></span></p>
            <div class="flex">
               <p><i class="fas fa-house"></i><span><?= $fetch_property['type']; ?></span></p>
               <p><i class="fas fa-tag"></i><span><?= $fetch_property['offer']; ?></span></p>
               <p><i class="fas fa-bed"></i><span><?= $fetch_property['bhk']; ?> BHK</span></p>
               <p><i class="fas fa-trowel"></i><span><?= $fetch_property['status']; ?></span></p>
               <p><i class="fas fa-couch"></i><span><?= $fetch_property['furnished']; ?></span></p>
               <p><i class="fas fa-maximize"></i><span><?= $fetch_property['carpet']; ?>sqft</span></p>
            </div>
            <div class="flex-btn">
               <a href="view_property.php?get_id=<?= $fetch_property['id']; ?>" class="btn">view property</a>
               <input type="submit" value="send enquiry" name="send" class="btn">
            </div>
         </div>
      </form>
      <?php
         }
      }else{
         echo '<p class="empty">no results found!</p>';
      }
      ?>

   </div>

</section>

<!-- listings section ends -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

<script>

document.querySelector('#filter-btn').onclick = () =>{
   document.querySelector('.filters').classList.add('active');
}

document.querySelector('#close-filter').onclick = () =>{
   document.querySelector('.filters').classList.remove('active');
}

</script>

</body>
</html>
