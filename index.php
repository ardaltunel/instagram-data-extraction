<?
$ch = curl_init ();
curl_setopt_array (
    $ch , [
            CURLOPT_URL            => 'https://www.instagram.com/' . $_GET['username'] . '/embed/' ,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Linux; Android 6.0.1; SM-G935S Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36' ,
            CURLOPT_RETURNTRANSFER => true
        ]
);
$output = curl_exec ($ch);
curl_close ($ch);

$photo     = '';
$username  = $_GET['username'];
$followers = 0;
$postCount = 0;
$posts     = [];

$regex = '@\\\"owner\\\":{\\\"id\\\":\\\"([0-9]+)\\\",\\\"profile_pic_url\\\":\\\"(.*?)\\\",\\\"username\\\":\\\"(.*?)\\\",\\\"followed_by_viewer\\\":(true|false),\\\"has_public_story\\\":(true|false),\\\"is_private\\\":(true|false),\\\"is_unpublished\\\":(true|false),\\\"is_verified\\\":(true|false),\\\"edge_followed_by\\\":{\\\"count\\\":([0-9]+)},\\\"edge_owner_to_timeline_media\\\":{\\\"count\\\":([0-9]+)@';
preg_match ($regex , $output , $result);

if (isset($result[2])) {
    $photo = str_replace ('\\\\\\' , '' , $result[2]);
}
if (isset($result[9])) {
    $followers = $result[9];
}
if (isset($result[10])) {
    $postCount = $result[10];
}


preg_match_all ('@\\\"thumbnail_src\\\":\\\"(.*?)\\\"@' , $output , $result);
$posts = array_map (
    function ($image) {
        return str_replace ('\\\\\\' , '' , $image);
    } , array_slice ($result[1] , 0 , 6)
);

/*
if (!file_exists(__DIR__ . '/' . $username . '.jpg') && $photo) {
    file_put_contents(__DIR__ . '/' . $username . '.jpg', file_get_contents($photo));
}

echo json_encode([
    'username' => $username,
    'photo' => $photo,
    'followers' => $followers,
    'postCount' => $postCount,
    'posts' => $posts
]);
exit
*/

if ($postCount >= 1000000) {
    $formatted_postCount = number_format ($postCount / 1000000 , ($postCount >= 10000000 ? 0 : 1)) . 'M';
}
elseif ($postCount >= 10000) {
    $formatted_postCount = number_format ($postCount / 1000 , ($postCount >= 100000 ? 0 : 1)) . 'K';
}
elseif ($postCount >= 1000) {
    $formatted_postCount = number_format ($postCount , 0 , '.' , '.');
}
else {
    $formatted_postCount = $postCount;
}

if ($followers >= 1000000) {
    $formatted_followers = number_format ($followers / 1000000 , ($followers >= 10000000 ? 0 : 1)) . 'M';
}
elseif ($followers >= 10000) {
    $formatted_followers = number_format ($followers / 1000 , ($followers >= 100000 ? 0 : 1)) . 'K';
}
elseif ($followers >= 1000) {
    $formatted_followers = number_format ($followers , 0 , '.' , '.');
}
else {
    $formatted_followers = $followers;
}

$photoInstagram     = file_get_contents ("$photo");
$photoInstagramData = base64_encode ($photoInstagram);
?>

<!doctype html>
<html lang="en">
<head>
    <title>Instagram - Arda Altunel</title>
    <?= $Meta ?><?= $GoogleTag ?><?= $GoogleAdSanse ?><?= $MetaIcons ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/1.4.6/tailwind.min.css">

    <link rel="stylesheet" href="CssJs/style.css">
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
                         src="data:image/jpeg;base64,<?= $photoInstagramData ?>"
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
                            <span class="font-semibold"><?= $formatted_postCount ?></span>
                            posts
                        </li>

                        <li>
                            <span class="font-semibold"><?= $formatted_followers ?></span>
                            followers
                        </li>
                        <li style="display: none;">
                            <span class="font-semibold">302</span>
                            following
                        </li>
                    </ul>

                    <!-- user meta form medium screens -->
                    <div class="hidden md:block" style="display: none;">
                        <h1 class="font-semibold">Mr Travlerrr...</h1>
                        <span>Travel, Nature and Music</span>
                        <p>Lorem ipsum dolor sit amet consectetur</p>
                    </div>

                </div>

                <!-- user meta form small screens -->
                <div class="md:hidden text-sm my-2" style="display: none;">
                    <h1 class="font-semibold">Mr Travlerrr...</h1>
                    <span>Travel, Nature and Music</span>
                    <p>Lorem ipsum dolor sit amet consectetur</p>
                </div>

            </header>

            <!-- posts -->
            <div class="px-px md:px-3">

                <!-- user following for mobile only -->
                <ul class="flex md:hidden justify-around space-x-8 border-t
                text-center p-2 text-gray-600 leading-snug text-sm">
                    <li>
                        <span class="font-semibold text-gray-800 block"><?= $formatted_postCount ?></span>
                        posts
                    </li>

                    <li>
                        <span class="font-semibold text-gray-800 block"><?= $formatted_followers ?></span>
                        followers
                    </li>
                    <li style="display: none;">
                        <span class="font-semibold text-gray-800 block">302</span>
                        following
                    </li>
                </ul>

                <!-- insta freatures -->
                <ul class="flex items-center justify-around md:justify-center space-x-12
                    uppercase tracking-widest font-semibold text-xs text-gray-600
                    border-t" style="display: none;">
                    <!-- posts tab is active -->
                    <li class="md:border-t md:border-gray-700 md:-mt-px md:text-gray-700">
                        <a class="inline-block p-3" href="#">
                            <i class="fas fa-th-large text-xl md:text-xs"></i>
                            <span class="hidden md:inline">post</span>
                        </a>
                    </li>
                    <li>
                        <a class="inline-block p-3" href="#">
                            <i class="far fa-square text-xl md:text-xs"></i>
                            <span class="hidden md:inline">igtv</span>
                        </a>
                    </li>
                    <li>
                        <a class="inline-block p-3" href="#">
                            <i class="fas fa-user border border-gray-500
                             px-1 pt-1 rounded text-xl md:text-xs"></i>
                            <span class="hidden md:inline">tagged</span>
                        </a>
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
                    endforeach; ?>

                </div>
            </div>
        </div>
    </main>
    <?
}
?>

<script src="CssJs/script.js"></script>
</body>
</html>
