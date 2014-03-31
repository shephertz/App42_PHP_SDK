<?php

namespace com\shephertz\app42\paas\sdk\php;

use com\shephertz\app42\paas\sdk\php\session\SessionService;
use com\shephertz\app42\paas\sdk\php\review\ReviewService;
use com\shephertz\app42\paas\sdk\php\review\Review;
use com\shephertz\app42\paas\sdk\php\upload\UploadService;
use com\shephertz\app42\paas\sdk\php\upload\UploadFileType;
use com\shephertz\app42\paas\sdk\php\user\UserService;
use com\shephertz\app42\paas\sdk\php\recommend\Recommender;
use com\shephertz\app42\paas\sdk\php\recommend\RecommenderService;
use com\shephertz\app42\paas\sdk\php\shopping\PaymentStatus;
use com\shephertz\app42\paas\sdk\php\shopping\Cart;
use com\shephertz\app42\paas\sdk\php\appTab\LicenseService;
use com\shephertz\app42\paas\sdk\php\appTab\Bill;
use com\shephertz\app42\paas\sdk\php\appTab\BillService;
use com\shephertz\app42\paas\sdk\php\appTab\Usage;
use com\shephertz\app42\paas\sdk\php\appTab\UsageService;
use com\shephertz\app42\paas\sdk\php\shopping\CartService;
use com\shephertz\app42\paas\sdk\php\shopping\CatalogueService;
use com\shephertz\app42\paas\sdk\php\shopping\Catalogue;
use com\shephertz\app42\paas\sdk\php\charge\Charge;
use com\shephertz\app42\paas\sdk\php\storage\Storage;
use com\shephertz\app42\paas\sdk\php\storage\StorageService;
use com\shephertz\app42\paas\sdk\php\geo\GeoService;
use com\shephertz\app42\paas\sdk\php\geo\Geo;
use com\shephertz\app42\paas\sdk\php\geo\GeoPoint;
use com\shephertz\app42\paas\sdk\php\message\QueueService;
use com\shephertz\app42\paas\sdk\php\message\Queue;
use com\shephertz\app42\paas\sdk\php\gallery\Album;
use com\shephertz\app42\paas\sdk\php\gallery\AlbumService;
use com\shephertz\app42\paas\sdk\php\gallery\PhotoService;
use com\shephertz\app42\paas\sdk\php\email\EmailService;
use com\shephertz\app42\paas\sdk\php\email\Email;
use com\shephertz\app42\paas\sdk\php\email\EmailMIME;
use com\shephertz\app42\paas\sdk\php\game\Game;
use com\shephertz\app42\paas\sdk\php\game\GameService;
use com\shephertz\app42\paas\sdk\php\game\Reward;
use com\shephertz\app42\paas\sdk\php\game\RewardService;
use com\shephertz\app42\paas\sdk\php\game\ScoreService;
use com\shephertz\app42\paas\sdk\php\game\ScoreBoardService;
use com\shephertz\app42\paas\sdk\php\log\Log;
use com\shephertz\app42\paas\sdk\php\log\LogService;
use com\shephertz\app42\paas\sdk\php\imageProcessor\Image;
use com\shephertz\app42\paas\sdk\php\imageProcessor\ImageProcessorService;
use com\shephertz\app42\paas\sdk\php\social\Social;
use com\shephertz\app42\paas\sdk\php\social\SocialService;
use com\shephertz\app42\paas\sdk\php\push\PushNotificationService;
use com\shephertz\app42\paas\sdk\php\appTab\DiscountService;
use com\shephertz\app42\paas\sdk\php\appTab\SchemeService;
use com\shephertz\app42\paas\sdk\php\appTab\PackageService;

//use com\shephertz\app42\paas\sdk\php\appTab\Discount;
include_once 'catalogue.php';

include_once 'Config.php';
include_once 'UserService.php';
include_once 'UploadService.php';
include_once 'Upload.php';
include_once 'UploadFileType.php';
include_once 'ReviewService.php';
include_once 'Review.php';
include_once 'SessionService.php';
include_once 'Recommender.php';
include_once 'RecommenderService.php';
include_once 'Cart.php';
include_once 'PaymentStatus.php';
include_once 'LicenseService.php';
include_once 'Usage.php';
include_once 'UsageService.php';
include_once 'BandWidthUnit.php';
include_once 'StorageUnit.php';
include_once 'TimeUnit.php';
include_once 'CartService.php';
include_once 'Cart.php';
include_once 'Charge.php';
include_once 'Storage.php';
include_once 'StorageService.php';
include_once 'Geo.php';
include_once 'GeoService.php';
include_once 'GeoPoint.php';
include_once 'QueueService.php';
include_once 'Queue.php';
include_once 'Album.php';
include_once 'AlbumService.php';
include_once 'PhotoService.php';
include_once 'EmailService.php';
include_once 'Email.php';
include_once 'EmailMIME.php';
include_once 'GameService.php';
include_once 'RewardService.php';
include_once 'ScoreService.php';
include_once 'ScoreBoardService.php';
include_once 'LogService.php';
include_once 'CatalogueService.php';
include_once 'Image.php';
include_once 'ImageProcessorService.php';
include_once 'Bill.php';
include_once 'BillService.php';
include_once 'Social.php';
include_once 'SocialService.php';
include_once 'PushNotificationService.php';
include_once 'DiscountService.php';
include_once 'SchemeService.php';
include_once 'PackageService.php';

/**
 * This class basically is a factory class which builds the service for use.
 * All services can be instantiated using this class
 * 
 */
class ServiceAPI {

    protected $apiKey;
    protected $secretKey;
    protected $url;
    protected $contentType;
    protected $accept;

    /**
     * this is a constructor that takes
     * @param  apiKey
     * @param  secretKey
     *
     */
    public function __construct($apiKey, $secretKey) {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->url = 'https://api.shephertz.com/cloud/';
        //$this->url = 'http://localhost:8082/App42_API_SERVER/cloud/';
    }

    // BUILDING FUNCTIONS FOR ALL THE API'S

    public function buildUserService() {
        $objUser = new UserService($this->apiKey, $this->secretKey, $this->url);
        return $objUser;
    }

    public function buildUploadService() {
        $objUpload = new UploadService($this->apiKey, $this->secretKey, $this->url);
        return $objUpload;
    }

    public function buildReviewService() {
        $objReview = new ReviewService($this->apiKey, $this->secretKey, $this->url);
        return $objReview;
    }

    public function buildSessionService() {
        $objSession = new SessionService($this->apiKey, $this->secretKey, $this->url);
        return $objSession;
    }

    public function buildRecommenderService() {
        $objRecommender = new RecommenderService($this->apiKey, $this->secretKey, $this->url);
        return $objRecommender;
    }

    public function buildCartService() {
        $objCart = new CartService($this->apiKey, $this->secretKey, $this->url);
        return $objCart;
    }

    public function buildLicenseService() {
        $objLicense = new LicenseService($this->apiKey, $this->secretKey, $this->url);
        return $objLicense;
    }

    public function buildUsageService() {
        $objUsage = new UsageService($this->apiKey, $this->secretKey, $this->url);
        return $objUsage;
    }

    public function buildCatalogueService() {
        $objCatalogue = new CatalogueService($this->apiKey, $this->secretKey, $this->url);
        return $objCatalogue;
    }

    public function buildChargeService() {
        $objCharge = new Charge($this->apiKey, $this->secretKey, $this->url);
        return $objCharge;
    }

    public function buildStorageService() {
        $objStorage = new StorageService($this->apiKey, $this->secretKey, $this->url);
        return $objStorage;
    }

    public function buildGeoService() {
        $objGeo = new GeoService($this->apiKey, $this->secretKey, $this->url);
        return $objGeo;
    }

    public function buildQueueService() {
        $objQueue = new QueueService($this->apiKey, $this->secretKey, $this->url);
        return $objQueue;
    }

    public function buildAlbumService() {
        $objAlbum = new AlbumService($this->apiKey, $this->secretKey, $this->url);
        return $objAlbum;
    }

    public function buildPhotoService() {
        $objPhoto = new PhotoService($this->apiKey, $this->secretKey, $this->url);
        return $objPhoto;
    }

    public function buildEmailService() {
        $objEmail = new EmailService($this->apiKey, $this->secretKey, $this->url);
        return $objEmail;
    }

    public function buildGameService() {
        $objGame = new GameService($this->apiKey, $this->secretKey, $this->url);
        return $objGame;
    }

    public function buildRewardService() {
        $objReward = new RewardService($this->apiKey, $this->secretKey, $this->url);
        return $objReward;
    }

    public function buildScoreService() {
        $buildScore = new ScoreService($this->apiKey, $this->secretKey, $this->url);
        return $buildScore;
    }

    public function buildScoreBoardService() {
        $buildScoreBoard = new ScoreBoardService($this->apiKey, $this->secretKey, $this->url);
        return $buildScoreBoard;
    }

    public function buildLogService() {
        $buildLog = new LogService($this->apiKey, $this->secretKey, $this->url);
        return $buildLog;
    }

    public function buildImageProcessorService() {
        $buildImageProcessor = new ImageProcessorService($this->apiKey, $this->secretKey, $this->url);
        return $buildImageProcessor;
    }

    public function buildBillService() {
        $buildBill = new BillService($this->apiKey, $this->secretKey, $this->url);
        return $buildBill;
    }

    public function buildSocialService() {
        $buildSocial = new SocialService($this->apiKey, $this->secretKey, $this->url);
        return $buildSocial;
    }

    public function buildPushNotificationService() {
        $pushSocial = new PushNotificationService($this->apiKey, $this->secretKey, $this->url);
        return $pushSocial;
    }

    public function buildDiscountService() {
        $disUsage = new DiscountService($this->apiKey, $this->secretKey, $this->url);
        return $disUsage;
    }

    public function buildSchemeService() {
        $schemeObj = new SchemeService($this->apiKey, $this->secretKey, $this->url);
        return $schemeObj;
    }

    public function buildPackageService() {
        $packageObj = new PackageService($this->apiKey, $this->secretKey, $this->url);
        return $packageObj;
    }

}

?>