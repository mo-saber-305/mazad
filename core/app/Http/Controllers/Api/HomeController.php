<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\HomeBannerResource;
use App\Http\Resources\HomeBidResource;
use App\Http\Resources\HomeCounterResource;
use App\Http\Resources\HomeFaqResource;
use App\Http\Resources\HomeFeatureResource;
use App\Http\Resources\HomeQuickBannerResource;
use App\Http\Resources\HomeSponsorResource;
use App\Http\Resources\HomeTestimonialResource;
use App\Http\Resources\PostsResource;
use App\Http\Resources\ProductsResource;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $data = [];
        $banner = getContent('banner.content', true);
        $data['banner'] = new HomeBannerResource($banner);

        $features = getContent('feature.element');
        $data['features'] = HomeFeatureResource::collection($features);

        $category = getContent('categories.content', true);
        $data['categories']['heading'] = $category->data_values->heading;
        $data['categories']['subheading'] = $category->data_values->subheading;
        $categories = \App\Models\Category::where('status', 1)->withCount('products')->latest()->limit(5)->get();
        $data['categories']['lists'] = CategoriesResource::collection($categories);


        $liveAuction = getContent('live_auction.content', true);
        $data['live_auction']['heading'] = $liveAuction->data_values->heading;
        $data['live_auction']['subheading'] = $liveAuction->data_values->subheading;
        $liveProducts = \App\Models\Product::live()->latest()->limit(8)->get();
        $data['live_auction']['lists'] = ProductsResource::collection($liveProducts);

        $upcomingAuction = getContent('upcoming_auction.content', true);
        $data['upcoming_auction']['heading'] = $upcomingAuction->data_values->heading;
        $data['upcoming_auction']['subheading'] = $upcomingAuction->data_values->subheading;
        $upcomingProducts = \App\Models\Product::where('started_at', '>', now())->where('status', 1)->latest()->limit(8)->get();
        $data['upcoming_auction']['lists'] = ProductsResource::collection($upcomingProducts);

        $expiredAuction = getContent('recently_expired.content', true);
        $data['expired_auction']['heading'] = $expiredAuction->data_values->heading;
        $data['expired_auction']['subheading'] = $expiredAuction->data_values->subheading;
        $recentlyExpiredProducts = \App\Models\Product::where('expired_at', '<', now())->latest()->limit(4)->get();
        $data['expired_auction']['lists'] = ProductsResource::collection($recentlyExpiredProducts);

        $quickBanners = getContent('quick_banner.element');
        $data['quick_banners'] = HomeQuickBannerResource::collection($quickBanners);

        $counters = getContent('counter.element');
        $data['counters'] = HomeCounterResource::collection($counters);

        $bid = getContent('how_to_bid.content', true);
        $data['bids']['heading'] = $bid->data_values->heading;
        $data['bids']['subheading'] = $bid->data_values->subheading;
        $bids = getContent('how_to_bid.element');
        $data['bids']['lists'] = HomeBidResource::collection($bids);

        $testimonial = getContent('testimonial.content', true);
        $data['testimonials']['heading'] = $testimonial->data_values->heading;
        $data['testimonials']['subheading'] = $testimonial->data_values->subheading;
        $testimonials = getContent('testimonial.element');
        $data['testimonials']['lists'] = HomeTestimonialResource::collection($testimonials);

        $blog = getContent('blog.content', true);
        $data['blogs']['heading'] = $blog->data_values->heading;
        $data['blogs']['subheading'] = $blog->data_values->subheading;
        $blogs = getContent('blog.element', false, 3);
        $data['blogs']['lists'][] = PostsResource::collection($blogs);

        $faq = getContent('faq.content', true);
        $data['faqs']['heading'] = $faq->data_values->heading;
        $data['faqs']['subheading'] = $faq->data_values->subheading;
        $faqs = getContent('faq.element');
        $data['faqs']['lists'] = HomeFaqResource::collection($faqs);

        $sponsors = getContent('sponsors.element');
        $data['sponsors'] = HomeSponsorResource::collection($sponsors);

        $notify = 'home data';
        return responseJson(200, 'success', $notify, $data);
    }
}
