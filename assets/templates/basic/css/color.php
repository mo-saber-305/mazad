<?php
header("Content-Type:text/css");
$color = "#f0f"; // Change your Color Here
$secondColor = "#ff8"; // Change your Color Here

function checkhexcolor($color)
{
    return preg_match('/^#[a-f0-9]{6}$/i', $color);
}

if (isset($_GET['color']) and $_GET['color'] != '') {
    $color = "#" . $_GET['color'];
}

if (!$color or !checkhexcolor($color)) {
    $color = "#336699";
}


function checkhexcolor2($secondColor)
{
    return preg_match('/^#[a-f0-9]{6}$/i', $secondColor);
}

if (isset($_GET['secondColor']) and $_GET['secondColor'] != '') {
    $secondColor = "#" . $_GET['secondColor'];
}

if (!$secondColor or !checkhexcolor2($secondColor)) {
    $secondColor = "#336699";
}
?>

.text--base, .mega-menu-icon .mega-icon, .change-language span, .auction__item-thumb .total-bids i, .client__item::after, .footer-wrapper .footer-widget .links li a::before, .footer-wrapper .footer-widget .links li a:hover, h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover, .nav--tabs li a.active, .product__single-item .meta-post .meta-item .social-share li a:hover, .filter-widget .title i, .price-range label, .vendor__item .read-more, .vendor__item .vendor__info li i, .author-icon, .contact-icon, .contact-area .contact-content .contact-content-botom .subtitle, .contact-area .contact-content .contact-content-botom .contact-info li .cont a, .contact-area .contact-content .contact-content-botom .contact-info li .icon, .side__menu-title, .counter-item .counter-header .title, .faq__item.open .faq__title .title, p a, .cookies-card__icon, .price-range input, .footer-contact li i, .recent-blog .blog__content .date, .blog-details-header .meta-1 li i, .section__header.icon__contain .section__title .icon{
color: <?php echo $color; ?> !important;
}

.cmn--btn, .btn--base, .badge--base, .bg--base, .scrollToTop, .footer-wrapper .footer-widget .title::after, .about-seller::after, .filter-widget .sub-title::after, .form--check .form-check-input:checked, .pagination .page-item.active span, .pagination .page-item.active a, .pagination .page-item:hover span, .pagination .page-item:hover a, .ticket__wrapper-title::after, .video__btn, .video__btn::before, .video__btn::after, .about--list li::before, .faq__item.open .faq__title .right--icon::before, .account__section-wrapper .account__section-content .section__header .section__title::after, .filter-btn, .filter-widget .ui-slider-range,.cmn--btn.active:hover, .read-more:hover::before, .dashboard__item .dashboard__icon{
background-color: <?php echo $color; ?> !important;
border: 2px solid <?php echo $color; ?>;
}

.nav--tabs li a.active, .sidebar-countdown li, .form--check .form-check-input:checked, .side__menu li a.active, .side__menu li a:hover, .cmn--btn.active, .account__section-content .form-control:focus {
border-color: <?php echo $color; ?> !important;
}

.client__item .client__content{
border: 1px dashed <?php echo $color; ?>33;
}
.owl-dots .owl-dot{
background: <?php echo $color; ?>66;
}
.section__header .progress--bar{
background: <?php echo $color; ?>4d;
}

*::selection {
background-color: <?php echo $color; ?>;
}


.how__item-icon {
animation: pulseCustom 1.5s linear infinite;
border: 1px dashed <?php echo $color; ?>4d;
}


@keyframes pulseCustom {
50% {
box-shadow: 0 0 5px rgba(19, 81, 04, 0.2), 0 0 10px rgba(93, 81, 04, 0.4), 0 0 15px rgba(3, 81, 24, 0.6), 0 0 20px <?php echo $color; ?>;
}
}

.how__item::before {
border-top: 2px dashed <?php echo $color; ?>4d;
}

.faq__item {
border: 1px dashed <?php echo $color; ?>59;
}

.form--control-2:focus{
border: 1px solid <?php echo $color; ?>66;
}

.auction__item {
box-shadow: 0 0 5px <?php echo $color; ?>b3;
}
.auction__item:hover {
box-shadow: 0 0 10px <?php echo $color; ?>e6;
}

.feature__item {
border: 1px dashed <?php echo $color; ?>4d;
box-shadow: 5px 5px 130px <?php echo $color; ?>4d;
}

.category__item {
box-shadow: 0 0 15px <?php echo $color; ?>4d;
}

.counter-item {
border: 1px dashed <?php echo $color; ?>33;
}

.vendor__item {
box-shadow: 0 0 5px <?php echo $color; ?>66;
}

.vendor__item .vendor__bottom .vendor-author {
box-shadow: 0 0 6px <?php echo $color; ?>e6;
}

.hero-section{
border-bottom: 1px dashed <?php echo $color; ?>1a;
}

.contact-area .contact-wrapper{
border: 1px dashed <?php echo $color; ?>4d;
}

@media (max-width: 991px){
.menu-area .menu li:hover, .menu-area .menu li.open{
background-color: <?php echo $color; ?>;
}
}

.dashboard__item{
box-shadow: 0 0 10px <?php echo $color; ?>1a;
border: 1px dashed <?php echo $color; ?>4d;
}

.spinner {
border-top: 4px solid <?php echo $color; ?>;
}

.category__item-icon {
color: <?php echo $color; ?>;
}

.feature__item-icon {
background: <?php echo $color; ?> !important;
}

.feature__item.bg--section:hover .feature__item-icon {
box-shadow: 0px 0px 50px <?php echo $color; ?>;
}

div[class*="col"]:nth-of-type(4n + 1) .feature__item-icon {
background: <?php echo $color; ?> !important;
border-color: <?php echo $color; ?> !important;
}

.vendor__single__author .content-area ul li i {
color: <?php echo $color; ?>;
}

.product__single-item .meta-post .meta-item .social-share li {
background-color: <?php echo $color; ?>;
}


.btn__grp .cmn--btn.active {
background-color: <?php echo $secondColor; ?> !important;
border-color: <?php echo $secondColor; ?> !important;
}

.btn__grp .cmn--btn:hover,
.auction-section .cmn--btn:hover ,
.categories-section .cmn--btn:hover ,
.btn__grp .cmn--btn.active:hover {
background-color: <?php echo $color; ?> !important;
border-color: <?php echo $color; ?> !important;
}

.auction__item-thumb .total-bids {
background-color: <?php echo $color; ?>;
}

.clients-section .client__item-parent,
.counter-section .counter-item,
.categories__wrapper .category__item,
.auction-section .slide-item .auction__item,
.quick-banner-section .quick-banner-item a ,
.post-item {
border: 3px solid <?php echo $color; ?>;
}

.clients-section .client__item-parent:after,
.clients-section .client__item-parent:before,
.counter-section .counter-item:after,
.categories__wrapper .category__item:after,
.categories__wrapper .category__item:before,
.auction-section .slide-item .auction__item:after,
.auction-section .slide-item .auction__item:before,
.quick-banner-section .quick-banner-item a:after ,
.quick-banner-section .quick-banner-item a:before ,
.post-item:after,
.post-item:before {
background-color: <?php echo $color; ?> !important;
}

.quick-banner-item .quick-banner-content .cmn--btn {
background: <?php echo $color; ?> !important;
}

.auction-section .slide-item .auction__item .auction__item-content .auction__item-countdown ,
.auction-section .slide-item .auction__item .auction__item-content .auction__item-title {
border-bottom: 2px dashed <?php echo $color; ?>;
}

.auction__item-countdown .inner__grp .total-price {
color: <?php echo $color; ?>;
}
