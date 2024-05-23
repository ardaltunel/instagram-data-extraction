<?php

if($_GET) $username = $_GET["username"];

if(isset($_GET["username"]) && $_GET["username"] != "")
{
    $user = "https://www.instagram.com/".$username."/";

    $parsedUrl = parse_url(trim($user));

    $scheme = $parsedUrl['scheme']."://";
    $host = $parsedUrl['host'];
    $path = $parsedUrl['path'];

    $user_path = explode('/', $path);
    $user_path = "/".$user_path[1];

    $user = $scheme.$host.$user_path;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "$user/embed/",
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Linux; Android 8.0.0; SM-G930F Build/R16NW; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/102.0.5005.78 Mobile Safari/537.36[FBAN/EMA;FBLC/nl_NL;FBAV/306.0.0.13.107;]',
        CURLOPT_RETURNTRANSFER => true
    ]);

    $output = curl_exec($ch);
    curl_close($ch);

    $posts = 0;
    $followers = 0;
    $name_surname = "";

    //Username and Profile Photo
    preg_match('@\\\"profile_pic_url\\\":\\\"(.*?)\\\",\\\"username\\\":\\\"(.*?)\\\"@',$output,$result);

    if(isset($result[1]) && isset($result[2]))
    {
        $photo = str_replace('\\\\\\','',$result[1]);
        $username = $result[2];
        $profile_path = "profile/$username/";

        if (!file_exists($profile_path)) {
            if (mkdir($profile_path, 0777, true)) {
                if(!file_exists($profile_path . "avatar.jpg"))
                {
                    file_put_contents($profile_path."avatar.jpg",file_get_contents($photo));
                }
            }
        }
        $photo = $profile_path."avatar.jpg";
    }

    //Name Surname
    preg_match('@\\\"followers_count\\\":([0-9,]+),\\\"full_name\\\":\\\"(.*?)\\\"@',$output,$result);
    if(isset($result[1]))
    {
        $followers = number_format($result[1],0,",");
    }
    if(isset($result[2]))
    {
        $name_surname = json_decode('"'.str_replace("\\\\","\\",$result[2]).'"');
    }


    //Followers and posts
    preg_match('@\\\"edge_owner_to_timeline_media\\\":{\\\"count\\\":([0-9]+)@i',$output,$result);

    if(isset($result[1]))
    {
        $posts = number_format($result[1],0,",");
    }

    $userlink = "https://instagram.com/".$username;
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>Instagram - Arda Altunel</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.4.6/tailwind.min.css">

    <style>
        body{
            margin: 0;
            padding: 0;
        }

        .pb-full {
            padding-bottom: 100%;
        }

        .search-bar:focus + .fa-search{
            display: none;
        }

        @media screen and (min-width: 768px) {
            .post:hover .overlay {
                display: block;
            }
        }

        .pleaseSearch {
            height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .editProfile {
            border: 0.1rem solid #dbdbdb;
            border-radius: 0.3rem !important;
            padding: 0 2.4rem !important;
            line-height: 1.8;
        }

        .pleaseSearchText {
            font-size: 3rem;
            font-weight: 100;
            text-align: center;
        }

        @media (max-width: 768px) {
            .pleaseSearchText {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

<nav class="border-b px-4 py-2 bg-white">
    <div class="flex flex-wrap items-center justify-between md:justify-around">
        <!-- logo -->
        <a href="/">
            <img class="h-10"
                 src="Images/instagram-logo.svg"
                 alt="Instagram">
        </a>

        <!-- search-->
        <form class="relative sm:block text-gray-500">
            <input class="search-bar max-w-xs border rounded bg-gray-200 px-4
            text-center outline-none focus:border-gray-400" name="username" type="search" placeholder="Search">

        </form>

        <div class="hidden space-x-4 sm:block">
            <a class="inline-block bg-blue-500 px-2 py-1 text-white font-semibold
                           text-sm rounded" href="#">Log In</a>
            <a class="inline-block text-blue-500 font-semibold text-sm" href="#">Sign Up</a>
        </div>
    </div>
</nav>
<?
if ($followers == null) {
    ?>
    <div class="pleaseSearch">
        <p class="pleaseSearchText">The Profile is Hidden or We Encounter a <br> Technical Problem Please Leave it Later and Try</p>
    </div>
    <?
}
else {
    ?>
    <main class="bg-gray-100 bg-opacity-25">

        <div class="lg:w-8/12 lg:mx-auto mb-8">

            <header class="flex flex-wrap items-center p-4 md:py-8">

                <div class="md:w-3/12 md:ml-16">
                    <!-- profile image -->
                    <img class="w-20 h-20 md:w-40 md:h-40 object-cover rounded-full border-2 border-pink-600 p-1"
                         src="<?= $photo ?>"
                         alt="Instagram Profile Picture">
                </div>

                <!-- profile meta -->
                <div class="w-8/12 md:w-7/12 ml-4">
                    <div class="md:flex md:flex-wrap md:items-center mb-4">
                        <h2 class="text-3xl inline-block font-light md:mr-4 mb-2 sm:mb-0">
                            <?= $username ?>
                        </h2>

                        <!-- follow button -->
                        <a href="#" class="bg-white-500 px-2 py-1
                        text-black font-semibold text-sm rounded block text-center
                        sm:inline-block block editProfile">Edit Profile</a>
                    </div>

                    <!-- post, following, followers list for medium screens -->
                    <ul class="hidden md:flex space-x-8 mb-4">
                        <li>
                            <span class="font-semibold"><?= $posts ?></span>
                            posts
                        </li>

                        <li>
                            <span class="font-semibold"><?= $followers ?></span>
                            followers
                        </li>
                    </ul>
                </div>
            </header>

            <!-- posts -->
            <div class="px-px md:px-3">

                <!-- user following for mobile only -->
                <ul class="flex md:hidden justify-around space-x-8 border-t
                text-center p-2 text-gray-600 leading-snug text-sm">
                    <li>
                        <span class="font-semibold text-gray-800 block"><?= $posts ?></span>
                        posts
                    </li>

                    <li>
                        <span class="font-semibold text-gray-800 block"><?= $followers ?></span>
                        followers
                    </li>
                </ul>

                <!-- flexbox grid -->
                <div class="flex flex-wrap -mx-px md:-mx-3">

                    <!-- column -->
                    <?php
                    foreach ($posts as $post): ?>
                        <?
                        $postInstagram     = file_get_contents ("$post");
                        $postInstagramData = base64_encode ($postInstagram);
                        ?>
                        <div class="w-1/3 p-px md:px-3">
                            <a href="#">
                                <article class="post bg-gray-100 text-white relative pb-full md:mb-6">
                                    <!-- post image-->
                                    <img class="w-full h-full absolute left-0 top-0 object-cover"
                                         src="data:image/jpeg;base64,<?= $postInstagramData ?>"
                                         alt="Instagram Post">
                                </article>
                            </a>
                        </div>
                    <?php
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </main>
    <?
}
?>
</body>
</html>
