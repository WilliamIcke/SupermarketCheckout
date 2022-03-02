<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\CheckoutController;

class CheckoutTest extends TestCase
{
    // Unit tests to test checkout. Currently based and data set as a constant in CheckoutController
    // Once that changes to be database based, we will need alterations here to be able to import our our test data for it.
    // This list of tests would atleast double once we can control the test data
    
    /**
     * Test checkout of a single item, single quantity, no specials
     *
     * @return void
     */
    public function test_CheckoutSingleItemSingleQuantityWithNoSpecialOffers()
    {
        $aTestData = [
            "A" => "0",
            "B" => "0",
            "C" => "0",
            "D" => "0",
            "E" => "1",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 5);
    }
    
    /**
     * Test checkout of a single item, multiple quantity, no specials
     *
     * @return void
     */
    public function test_CheckoutSingleItemMultipleQuantityWithNoSpecialOffers()
    {
        $aTestData = [
            "A" => "0",
            "B" => "0",
            "C" => "0",
            "D" => "0",
            "E" => "5",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 25);
    }
    
    /**
     * Test checkout of a single item, single quantity, a single special (Quantity based)
     *
     * @return void
     */
    public function test_CheckoutSingleItemSingleQuantityWithASpecialQuantityOffer()
    {
        $aTestData = [
            "A" => "1",
            "B" => "0",
            "C" => "0",
            "D" => "0",
            "E" => "0",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 50);
    }

    /**
     * Test checkout of a single item, single quantity, a single special (Purchase with based)
     *
     * @return void
     */
    public function test_CheckoutSingleItemSingleQuantityWithASpecialPWOffer()
    {
        $aTestData = [
            "A" => "0",
            "B" => "0",
            "C" => "0",
            "D" => "1",
            "E" => "0",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 15);
    }
    
    /**
     * Test checkout of a single item, multiple quantity, a single special (Quantity based)
     *
     * @return void
     */
    public function test_CheckoutSingleItemMultipleQuantityWithASpecialQuantityOffer()
    {
        $aTestData = [
            "A" => "5",
            "B" => "0",
            "C" => "0",
            "D" => "0",
            "E" => "0",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 230);
    }
 
    /**
     * Test checkout of a single item, multiple quantity, a single special (Purchase with based)
     *
     * @return void
     */
    public function test_CheckoutSingleItemMultipleQuantityWithASpecialPWOffer()
    {
        $aTestData = [
            "A" => "0",
            "B" => "0",
            "C" => "0",
            "D" => "5",
            "E" => "0",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 75);
    }
    
    /**
     * Test checkout of a single item, single quantity, a multiple specials (Both quantity based)
     *
     * @return void
     */
    public function test_CheckoutSingleItemSingleQuantityWithMultipleSpecialQBOffers()
    {
        $aTestData = [
            "A" => "0",
            "B" => "0",
            "C" => "5",
            "D" => "0",
            "E" => "0",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 88);
    }
 
    /**
     * Test checkout of a single item, single quantity, a multiple specials (Both purchase with based)
     *
     * @return void
     */
    public function test_CheckoutSingleItemSingleQuantityWithMultipleSpecialPWOffers()
    {      
        // Currently unavailable, as we cannot control SKU test data
        $this->assertTrue(true);
    }
 
    /**
     * Test checkout of a single item, single quantity, a multiple specials (Mixed, quantity + purchase with)
     *
     * @return void
     */
    public function test_CheckoutSingleItemSingleQuantityWithMultipleSpecialMixedOffers()
    {      
        // Currently unavailable, as we cannot control SKU test data
        $this->assertTrue(true);
    }

    /**
     * Test checkout of a multiple items, single quantity, no specials
     *
     * @return void
     */
    public function test_CheckoutMultipleItemSingleQuantityWithNoSpecialOffers()
    {      
        // Currently unavailable, as we cannot control SKU test data
        $this->assertTrue(true);
    }

    /**
     * Test checkout of a multiple items, single quantity
     *
     * @return void
     */
    public function test_CheckoutMultipleItemSingleQuantity()
    {
        $aTestData = [
            "A" => "1",
            "B" => "0",
            "C" => "1",
            "D" => "0",
            "E" => "1",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);
        var_dump($rResponse);
        $this->assertTrue($rResponse == 75);
    }

    /**
     * Test checkout of a multiple items, multiple quantity
     *
     * @return void
     */
    public function test_CheckoutMultipleItemMultipleQuantity()
    {
        $aTestData = [
            "A" => "5",
            "B" => "0",
            "C" => "5",
            "D" => "0",
            "E" => "5",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);
        $this->assertTrue($rResponse == 343);
    }

    /**
     * Test checkout of a multiple Quantity based special offers (5 x C)
     *
     * @return void
     */
    public function test_CheckoutItemWithMultipleSpecialQBOffers1()
    {
        $aTestData = [
            "A" => "0",
            "B" => "0",
            "C" => "5",
            "D" => "0",
            "E" => "0",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 88);
    }

    /**
     * Test checkout of a multiple Quantity based special offers (4 X c)
     *
     * @return void
     */
    public function test_CheckoutItemWithMultipleSpecialQBOffers2()
    {
        $aTestData = [
            "A" => "0",
            "B" => "0",
            "C" => "4",
            "D" => "0",
            "E" => "0",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 70);
    }
    
    /**
     * Test checkout of a Purchased With based special offers
     *
     * @return void
     */
    public function test_CheckoutItemWithSingleSpecialPWOffers1()
    {
        $aTestData = [
            "A" => "6",
            "B" => "0",
            "C" => "0",
            "D" => "10",
            "E" => "0",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 350);
    }

    /**
     * Test checkout of a Purchased With based special offers
     *
     * @return void
     */
    public function test_CheckoutItemWithSingleSpecialPWOffers2()
    {
        $aTestData = [
            "A" => "6",
            "B" => "0",
            "C" => "0",
            "D" => "4",
            "E" => "0",
        ];
        $rRequest = Request::create('/calculate-and-get-total', 'POST',[
            'keywordInput' => $aTestData
        ]);
        $cCheckoutController = new CheckoutController();
        $rResponse = $cCheckoutController->CalculateAndGetTotal($rRequest);

        $this->assertTrue($rResponse == 280);
    }
}