<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Cinema404</title>
</head>
<body>
    <section class="top-bar">
        <div class="left-content">
            <h2 class="title">Cinema404</h2>
        </div>
        <div class="right-content">
            <img src="assets/image/filter.png" alt="" class="filter">
            <img src="assets/image/card.png" alt="" class="cart">
            <img src="assets/image/help.png" alt="" class="help">
            <div class="profile-img-box">
                <img src="assets/image/profile.jpg" alt="">
            </div>
            <img src="assets/image/menu.png" alt="" class="menu">
        </div>
    </section>

    <section class="hero-banner">
        <div class="hero-overlay">
            <h1>Book Your Movie Experience Instantly</h1>
            <p>Choose your movie, select a seat, and enjoy the show.</p>
            <div class="hero-buttons">
                <a href="#reservationForm" class="btn">🎟️ Book Now</a>
            </div>
            <div class="hero-search">
                <input type="text" id="searchInputHero" placeholder="Cinema404.com" readonly>
            </div>
        </div>
    </section>