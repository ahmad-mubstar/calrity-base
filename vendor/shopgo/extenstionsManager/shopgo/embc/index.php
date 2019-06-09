<?php

    require 'inc/session.php';
    require 'helper.php';//all helper methods.

    $satisList  = getSatisList();//Get all available extensions list "by Satis".
    $compList   = getComList();//Get current extensions has been installed by Composer.
?>

<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Extensions Manager</title>

  <link rel="stylesheet" type="text/css" href="css/style.css">
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
  <script src="js/prefixfree.min.js"></script>
  <script type="text/javascript" src="js/javas.js"></script>

</head>

<body>
<div style="text-align: center;">
<h1 id="tag">Extensions Manager</h1>
<a href="logout.php">Logout</a>
</div>

<div id="wrapper">
  <img  id="loading" src="inc/loading.gif"/>
</div>
<main>

  <input id="tab1" type="radio" name="tabs" checked>
  <label for="tab1">Installed <<b id="countInstalled" style="color: #35b82a;"><?php echo count($compList); ?></b>></label>

  <input id="tab2" type="radio" name="tabs">
  <label for="tab2">Available APPs <<b id="countAvailable"style="color: #2012d3;"><?php echo count($satisList)-count($compList)?></b>></label>

  <input id="tab5" type="radio" name="tabs">
  <label for="tab5">Output</label>

  <section id="content1">
    <p>
    <table id="content" class="flat-table">
      <tr>
        <th>ID</th>
        <th>Extension</th>
        <th>Version</th>
        <th>Releases</th>
        <th>Depending On</th>
        <th>Delete</th>
      </tr>
      <?php foreach ($satisList as $key => $val){ $counter++;
        if (array_key_exists($key, $compList)) { ?>
          <tr id="<?php echo $counter;?>">
            <td><?php echo $counter; ?></td>
            <td ><?php echo "$key"; ?></td>
            <td><?php echo $compList[$key]; ?></td>
            <td ><select class="select-style" style="width: 100%"; id="<?php echo $key;?>" name="release">
                <?php foreach(array_keys($satisList[$key]) as $paramName) {
                  echo "<option value = ".$paramName." >". $paramName."</option >";
                }
                ?>
              </select>
            </td>

            <td><select  class="select-style" style="width: 100%"; name="require">
                <?php foreach(array_keys($satisList[$key]["dev-master"]["require"]) as $paramName2) {
                  echo "<option value = ".$paramName2." >". $paramName2."</option >";
                }
                ?>
              </select>
            </td>
            <td >
              <?php echo '<a  href="#tag"> <button id='.$counter.' class="delete" type="button" value='.$key.'>Delete </button></a>';
                    echo '<a  href="#tag"> <button id='.$counter.' class="update" type="button" value='.$key.'>Update </button></a>';
                    echo '<a  href="#tag"> <button id='.$counter.' class="reinstall" type="button" value='.$key.'>Reinstall </button></a>'
              ?>
            </td>
          </tr>
        <?php }
      } ?>
    </table>
    </p>
  </section>

  <section id="content2">
    <p>

    <table id="content">
      <tr>
        <th>ID</th>
        <th>Extension</th>
        <th>Releases</th>
        <th>Depending On</th>
        <th>Install</th>
      </tr>
      <?php   $counter=0;
              foreach ($satisList as $key => $val){ $counter++;
              if (!(array_key_exists($key, $compList))) { ?>
                <tr id="<?php echo $counter;?>">
                <td><?php echo $counter; ?></td>
                <td><?php echo "$key"; ?></td>
                <td><select class="select-style" style="width: 100%;" id="<?php echo $key; ?>" name="release">
                    <?php foreach (array_keys($satisList[$key]) as $paramName) {
                      echo "<option value = " . $paramName . " >" . $paramName . "</option >";
                    }
                    ?>
                  </select>
                </td>

                <td><select class="select-style" style="width: 100%;" name="require">
                    <?php foreach (array_keys($satisList[$key]["dev-master"]["require"]) as $paramName2) {
                      echo "<option value = " . $paramName2 . " >" . $paramName2 . "</option >";
                    }
                    ?>
                  </select>
                </td>
                <td >
                <?php echo '<a href="#tag"><button id='.$counter.' class="install" type="button" value='.$key.'>Install </button></a>';
              }
                ?>
                </td>
                </tr>
      <?php
          } ?>
    </table>
    </p>
  </section>
  <section id="content5">
    <p id="result">
    </p>
    <a  href="#tag"> <button class="symlinks" type="button" value=''> Force Allow Symlinks </button> </a>
  </section>

</main>
</body>
</html>
