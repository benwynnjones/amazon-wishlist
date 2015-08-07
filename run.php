<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
$dotenv->required(["AMAZON_TLD", "WISHLIST_ID", "WISHLIST_BUDGET", "WISHLIST_EMAIL"]);
$email = getenv("WISHLIST_EMAIL");

$selection = (new Tevben\Wishlist\Select())->pick();

$loader = new Twig_Loader_Filesystem(__DIR__);
$twig = new Twig_Environment($loader);

$content = $twig->render('template.html', $selection);

$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: Robot <'.$email.'>' . "\r\n";

mail($email, "Your movie selection for this week is...", $content, $headers);