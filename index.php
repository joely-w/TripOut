<?php
$title = "Home";
include('header.php'); ?>
<body>
<?php include('navigation.php'); ?>
<div class="hero">
    <div class="prev" id="prev">
        <div class="arrow-left"></div>
    </div>
    <ul class="slider">
        <li class="visible"> <!-- Current visible slide -->
            <img src="images/events/headers/background.jpg"/>
        </li>
        <li>
            <img src="https://www.tribaeast.com/wp-content/uploads/2019/04/Voyager-of-the-Sea-Promotion-Banner.jpg"/>
            <!--Hidden slide -->
        </li>
        <li>
            <img src="https://i2.wp.com/kgly.com/wp-content/uploads/2016/09/PS-Web-Banner-Love-Moves-1920x800-min.jpg"/>
            <!--Hidden slide -->
        </li>
        <li>
            <img src="https://myjoolzcouk-joolz.netdna-ssl.com/wp-content/uploads/2018/12/Joolz-Online-Banner-Made-for-Mums-Awards-2019-1920x700.jpg"/>
            <!--Hidden slide -->
        </li>
        <li>
            <img src="https://www.summergrove.org/760/wp-content/uploads/2017/07/MANGO-Shreveport-Website-Banner-1920x800.jpg"/>
            <!--Hidden slide -->
        </li>
    </ul>
    <div class="next" id="next">
        <div class="arrow-right"></div>
    </div>
</div>
<div class="container events">
    <h1>Events you might be interested in</h1>

    <div class="row">
        <div class="col-md-3 event-thumb"><img src="https://source.unsplash.com/random/400x400"/>
            <h3>Chatterley Whitfield Colliery Heritage Day</h3><span>Make sure your family avoid the mines though, mines are pretty dangerous things. But do come along, it'll be a lark, plus even if it's not it fills my coursework text filler so what ho!</span>
        </div>
        <div class="col-md-3 event-thumb"><img src="https://source.unsplash.com/random/500x500"/>
            <h3>Bonfire, lantern walk and fireworks</h3><span>Make sure your family avoid the mines though, mines are pretty dangerous things. But do come along, it'll be a lark, plus even if it's not it fills my coursework text filler so what ho!</span>
        </div>
        <div class="col-md-3 event-thumb"><img src="https://source.unsplash.com/random/200x200"/>
            <h3>NCS Graduation Event Stoke</h3><span>Make sure your family avoid the mines though, mines are pretty dangerous things. But do come along, it'll be a lark, plus even if it's not it fills my coursework text filler so what ho!</span>
        </div>
        <div class="col-md-3 event-thumb"><img src="https://source.unsplash.com/random/200x201"/>
            <h3>Greatest Showman Drive-In Screening</h3><span>Make sure your family avoid the mines though, mines are pretty dangerous things. But do come along, it'll be a lark, plus even if it's not it fills my coursework text filler so what ho!</span>
        </div>

    </div>

</div>
<?php
$scripts_footer = array("hero.js");
include('footer.php'); ?>
</body>