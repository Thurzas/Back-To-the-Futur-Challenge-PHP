<?php
session_start();
$presentTime = new DateTime();
$errors = [];

if(isset($_SESSION['destination'])){
    $interval = $_SESSION['destination']->diff($presentTime);
    $fuel = estimateFuel($interval);

    $targetArray = explode('-', $_SESSION['destination']->format('Y-m-d-g-i-A'));
    $target = [
        'MONTH'=>$targetArray[1],
        'DAY'=>$targetArray[2],
        'YEAR'=>$targetArray[0],
        'A'=>$targetArray[5],
        'HOUR'=>$targetArray[3],
        'MIN'=>$targetArray[4]
    ];
}

$presentTimeArray = explode('-', $presentTime->format('Y-m-d-g-i-A'));
$present = [
    'MONTH'=>$presentTimeArray[1],
    'DAY'=>$presentTimeArray[2],
    'YEAR'=>$presentTimeArray[0],
    'A'=>$presentTimeArray[5],
    'HOUR'=>$presentTimeArray[3],
    'MIN'=>$presentTimeArray[4]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destination = array_map('trim', $_POST);
    if (empty($destination['YEAR']) || !filter_var((int)$destination['YEAR'], FILTER_VALIDATE_INT)) {
        $errors[] = 'bad year param !';
    }
    if (empty($destination['MONTH']) || !filter_var((int)$destination['MONTH'], FILTER_VALIDATE_INT)) {
        $errors[] = 'bad month param !';
    }
    if (empty($destination['DAY']) || !filter_var((int)$destination['DAY'], FILTER_VALIDATE_INT)) {
        $errors[] = 'bad day param !';
    }
    if (empty($destination['HOUR']) || !filter_var((int)$destination['HOUR'], FILTER_VALIDATE_INT)) {
        $errors[] = 'bad hour param !';
    }
    if (empty($destination['MIN']) || !filter_var((int)$destination['MIN'], FILTER_VALIDATE_INT)) {
        $errors[] = 'bad min param !';
    }
    if ((int)($destination['DAY']) <1 || (int)$destination['DAY'] > 31) {
        $errors[] = 'day should be between 1 and 31 !';
    }
    if ((int)($destination['MONTH']) <1 || (int)($destination['MONTH']) > 12) {
        $errors[] = 'month should be between 1 and 12 !';
    }
    if ((int)($destination['MIN']) <1 || (int)($destination['MIN']) > 59) {
        $errors[] = 'min should be between 1 and 59 !';
    }
    if ((int)($destination['HOUR']) <1 || (int)($destination['HOUR']) > 12) {
        $errors[] = 'hour should be between 1 and 12 !';
    }
    if (empty($errors)) {
        $destinationTime = (new DateTime())->setDate($destination['YEAR'],$destination['MONTH'],$destination['DAY']);
        if($destination['daytime']=='PM')
            $destinationTime->add(new DateInterval('PT12H'));
        //$destinationTime->setTime($destination['HOUR'],$destination['MIN']);
        $_SESSION['destination'] = $destinationTime;
        header('Location: /index.php');
        exit();
    }
}

function estimateFuel(DateInterval $time):int
{
    $timeInMinute = $time-> i + $time->h*60 + $time->days*24*60; 
    return ($timeInMinute / 10000);   
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
    <title>Let's go back to the future !</title>
</head>
<body>    
    <form class="component" method="post">
        <?php if (!empty($errors)) : ?>
            <h3>Please fix errors below</h3>
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <div class="component">
        <?php foreach($present as $key => $info) :?>
 
            <?php if($info == 'AM' || $info == 'PM') :?>
                <div class="wrapper radio-buttons">
                    <label for="AM"><p class="label">AM</p>
                        <input type="radio" id="AM" name="daytime" value="AM" <?= $present['A']=='AM' ? '': 'checked="checked"' ?> />
                        <span class="checkmark"></span>
                    </label>
                    <label for="PM"><p class="label">PM</p>
                        <input type="radio" id="PM" name="daytime" value="PM" <?= $present['A']=='PM' ? '': 'checked="checked"' ?> />
                        <span class="checkmark"></span>
                    </label>
                </div>
            <?php else :?>
            <div class="wrapper-form">
                <p class="label">
                    <label ><?= $key ?> :</label>
                </p>
                <?php if($key == 'YEAR') :?>
                    <p><input class="display-form form-year" type="text" id="<?= $key ?>" name="<?= $key ?>" value="<?= $info ?? '' ?>" required>
                    <?php elseif($key == 'HOUR'): ?>
                    <p><input class="display-form form-H" type="text" id="<?= $key ?>" name="<?= $key ?>" value="<?= $info ?? '' ?>" required>
                    <?php elseif($key == 'MIN'): ?>
                    <p><input class="display-form form-min" type="text" id="<?= $key ?>" name="<?= $key ?>" value="<?= $info ?? '' ?>" required>
                <?php else :?>
                    <p><input class="display-form form-date" type="text" id="<?= $key ?>" name="<?= $key ?>" value="<?= $info ?? '' ?>" required>
                <?php endif; ?>
            </div>  
            <?php endif; ?>
        <?php endforeach; ?>
    </div>  
        <button class="button" type="submit">LET'S GO !</button>
        <h2 class="large-label">Destination Time</h2>
    </form> 
    <div class="component">
        <?php foreach($present as $key => $info) :?>
            <?php if($info == 'AM' || $info == 'PM') :?>
                <div class="wrapper">
                    <p class="label">AM</p>
                    <p class="<?= $present['A']=='AM' ? 'lit':'unlit' ?>"></p>
                    <p class="label">PM</p>
                    <p class="<?= $present['A']=='PM' ? 'lit':'unlit' ?>"></p>
                </div>
            <?php else :?>
            <div class="wrapper">
                <p class="label"><?= $key ?> :</p>
                <p class="display"><?=  $info ?></p>
            </div>  
            <?php endif;?>
        <?php endforeach; ?>  
        <h2 class="large-label">Present Time</h2>
    </div>
    
    <?php if(isset($target)) :?>
        <div class="component">
            <?php foreach($target as $key => $info) :?>
                <?php if($info == 'AM' || $info == 'PM') :?>
                    <div class="wrapper">
                        <p class="label">AM</p>
                        <p class="<?= $target['A']=='AM' ? 'lit':'unlit' ?>"></p>
                        <p class="label">PM</p>
                        <p class="<?= $target['A']=='PM' ? 'lit':'unlit' ?>"></p>
                    </div>
                <?php else :?>
                <div class="wrapper">
                    <p class="label"><?= $key ?> :</p>
                    <p class="display-last"><?=  $info ?></p>
                </div>  
                <?php endif;?>
            <?php endforeach; ?>  
            <h2 class="large-label">Last Time targeted</h2>
        </div>
    <?php endif;?>
        <?php if(isset($interval) && isset($fuel)) :?>
            <div class="component">
                <div class="wrapper">
                    <p class="label">Years</p>
                    <p class="display form-min"><?= $interval->y?></p>
                </div>
                <div class="wrapper">
                    <p class="label">Months</p>
                    <p class="display form-min"><?= $interval->m?></p>
                </div>
                <div class="wrapper">
                    <p class="label">Days</p>
                    <p class="display form-min"><?= $interval->d?></p>
                </div>
                <div class="wrapper">
                    <p class="label">Hours</p>
                    <p class="display"><?= $interval->h?></p>
                </div>
                <div class="wrapper">
                    <p class="label">Minutes</p>
                    <p class="display form-min"><?= $interval->i?></p>
                </div>
                <h2 class="large-label">Prepare <?= (string)$fuel ?> L for this route.</h2>
            </div>    
            <?php session_unset() ?>
        <?php endif;?>
    </body>
</html>