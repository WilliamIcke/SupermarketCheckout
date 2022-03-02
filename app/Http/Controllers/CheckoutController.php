<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('index');
    }

    // Special Price Types
    const QUANTITY_BASED = 1;
    const PURCHASED_WITH = 2;

    const SKUs = [
        "A" => [
            "UnitPrice" => 50,
            "SpecialPrice" => [
                [
                    "Type" => self::QUANTITY_BASED,
                    "SpecialCondition" => 3,
                    "Price" => 130
                ]
            ]
        ],
        "B" => [
            "UnitPrice" => 30,
            "SpecialPrice" => [
                [
                    "Type" => self::QUANTITY_BASED,
                    "SpecialCondition" => 2,
                    "Price" => 45
                ]
            ]
        ],
        "C" => [
            "UnitPrice" => 20,
            "SpecialPrice" => [
                [
                    "Type" => self::QUANTITY_BASED,
                    "SpecialCondition" => 2,
                    "Price" => 38
                ],
                [
                    "Type" => self::QUANTITY_BASED,
                    "SpecialCondition" => 3,
                    "Price" => 50
                ]
            ]
        ],
        "D" => [   
            "UnitPrice" => 15,
            "SpecialPrice" => [
                [
                    "Type" => self::PURCHASED_WITH,
                    "SpecialCondition" => "A",
                    "Price" => 5,
                ]
            ]
        ],
        "E" => [
            "UnitPrice" => 5,
            "SpecialPrice" => [
            ]
        ],

    ];

    public function CalculateAndGetTotal(Request $request): int {
        $iTotalCost = 0;
        foreach ($request->keywordInput as $sItem => $iQuantity) {
            if ($iQuantity > 0) {
                $iTotalCost += $this->CalculateItemTotal($sItem, $iQuantity, $request->keywordInput);
            }
        }

        return $iTotalCost;
    }

    private function CalculateItemTotal(string $sItem, int $iQuantity, array $aAllItemQuantities): int {
        // Check if there are any special offers
        $iItemUnitPrice = self::SKUs[$sItem]["UnitPrice"];
        $aItemSpecialOffers = self::SKUs[$sItem]["SpecialPrice"];

        switch (true) {
            // If one, trigger CalculateCostWithASpecialOffer
            case (count($aItemSpecialOffers) == 1):
                return $this->CalculateCostWithASpecialOffer($iQuantity, $iItemUnitPrice, $aItemSpecialOffers[0], $aAllItemQuantities, []);
                break;
            // If more than one, trigger CalculatesCostWithMoreThanOneSpecialOffers
            case (count($aItemSpecialOffers) > 1):
                return $this->CalculatesCostWithMoreThanOneSpecialOffers($iQuantity, $iItemUnitPrice, $aAllItemQuantities, $aItemSpecialOffers);
                break;
            // If none, trigger CalculateCost (This will also be the default)
            case (count($aItemSpecialOffers) == 0):
            default:
                return $this->CalculateCost($iQuantity, $iItemUnitPrice);
        }   
    }

    /**
     * This function returns total cost with no special offers
     */
    private function CalculateCost(int $iQuantity, int $iItemUnitPrice): int {
        return $iQuantity * $iItemUnitPrice;
    }

    /**
     * This function checks the special type and calls the necessary function
     */
    private function CalculateCostWithASpecialOffer(int $iQuantity, int $iItemUnitPrice, array $aTargetSpecialOffer, array $aAllItemQuantities, array $aItemSpecialOffers): int {
        switch ($aTargetSpecialOffer["Type"]) {
            case self::PURCHASED_WITH:
                return $this->CalculatesCostWithSpecialOfferPurchasedWith($iQuantity, $iItemUnitPrice, $aTargetSpecialOffer, $aAllItemQuantities, $aItemSpecialOffers);
                break;
            case self::QUANTITY_BASED:
            default:
                return $this->CalculateCostWithAQuantityBasedSpecialOffer($iQuantity, $iItemUnitPrice, $aTargetSpecialOffer, $aAllItemQuantities, $aItemSpecialOffers);
        };
    }

    /**
     * This function calculates the total cost using a single special offer
     * It will firstly get how many items are eligible and not eligible, then calculates cost and returns
     */
    private function CalculateCostWithAQuantityBasedSpecialOffer(int $iQuantity, int $iItemUnitPrice, array $aTargetSpecialOffer, array $aAllItemQuantities, array $aItemSpecialOffers): int {
        // Use the quotient and remainder to check which items can be bought with the offer
        $aCheckItemsEligibleForOffer = $this->GetQuotientAndRemainder(
            $iQuantity,
            $aTargetSpecialOffer["SpecialCondition"]
        );

        $iQuantityEligibleForOffer = $aCheckItemsEligibleForOffer["Quotient"];
        $iQuantityNotEligibleForOffer = $aCheckItemsEligibleForOffer["Remainder"];

        if ($this->CheckIfEligibleForOtherOffers($aItemSpecialOffers, $iQuantityNotEligibleForOffer)) {
            return ($this->CalculateCost($iQuantityEligibleForOffer, $aTargetSpecialOffer["Price"])
                    + $this->CalculatesCostWithMoreThanOneSpecialOffers($iQuantityNotEligibleForOffer, $iItemUnitPrice, $aAllItemQuantities, $aItemSpecialOffers));
        } else {
            return ($this->CalculateCost($iQuantityEligibleForOffer, $aTargetSpecialOffer["Price"])
                    + $this->CalculateCost($iQuantityNotEligibleForOffer, $iItemUnitPrice));
        }
    }

    
    public function CalculatesCostWithSpecialOfferPurchasedWith(int $iQuantity, int $iItemUnitPrice, array $aTargetSpecialOffer, array $aAllItemQuantities, array $aItemSpecialOffers): int
    {
        $iItemPurchasedWithTargetQuantity = $aAllItemQuantities[$aTargetSpecialOffer["SpecialCondition"]];
        if ($iItemPurchasedWithTargetQuantity == 0) {
            return $this->CalculateCost($iQuantity, $iItemUnitPrice);
        }

        if ($iItemPurchasedWithTargetQuantity >= $iQuantity) {
            return $iQuantity * $aTargetSpecialOffer["Price"];
        }

        $iQuantityNotEligibleForOffer = $iQuantity - $iItemPurchasedWithTargetQuantity;
        $aAllItemQuantities[$aTargetSpecialOffer["SpecialCondition"]] = 0;

        if ($this->CheckIfEligibleForOtherOffers($aItemSpecialOffers, $iQuantityNotEligibleForOffer)) {
            return ($this->CalculateCost($iItemPurchasedWithTargetQuantity, $aTargetSpecialOffer["Price"])
                    + $this->CalculatesCostWithMoreThanOneSpecialOffers($iQuantityNotEligibleForOffer, $iItemUnitPrice, $aAllItemQuantities, $aItemSpecialOffers));
        } else {
            return ($this->CalculateCost($iItemPurchasedWithTargetQuantity, $aTargetSpecialOffer["Price"])
                    + $this->CalculateCost($iQuantityNotEligibleForOffer, $iItemUnitPrice));
        }
        
    }

    /**
     * This function calculates the lowest total cost of an item with multiple offers
     * Cycles through each of the special offers and records then returns the lowest found.
     */
    private function CalculatesCostWithMoreThanOneSpecialOffers(int $iQuantity, int $iItemUnitPrice, array $aAllItemQuantities, array $aItemSpecialOffers): int {
        $iLowestTotalCost = 0;
        foreach ($aItemSpecialOffers as $aTargetSpecialOffer) {
            // Skip if offer cannot be applied
            if (!$this->CheckIfCurrentOfferIsApplicable($aTargetSpecialOffer, $iQuantity)) {
                $iTotalCost = $this->CalculateCost($iQuantity, $iItemUnitPrice);
                if ($iLowestTotalCost === 0 || $iTotalCost < $iLowestTotalCost) {
                    $iLowestTotalCost = $iTotalCost;
                }
                continue;
            }
            // Track item quantities used in offer calculations
            $iSpecialOfferTotalCost = $this->CalculateCostWithASpecialOffer($iQuantity, $iItemUnitPrice, $aTargetSpecialOffer, $aAllItemQuantities, $aItemSpecialOffers);
            if ($iLowestTotalCost === 0 || $iSpecialOfferTotalCost < $iLowestTotalCost) {
                $iLowestTotalCost = $iSpecialOfferTotalCost;
            }
        }

        return $iLowestTotalCost;
    }

    private function GetQuotientAndRemainder(int $iDivisor, int $iDividend): array {
        $iQuotient = (int)($iDivisor / $iDividend);
        $iRemainder = $iDivisor % $iDividend;
        return [
            "Quotient" => $iQuotient,
            "Remainder" => $iRemainder 
        ];
    }

    private function CheckIfEligibleForOtherOffers(array $aItemSpecialOffers, int $iQuantityNotEligibleForOffer): bool {
        foreach($aItemSpecialOffers as $aSpecialOfferDetails) {
            if ($this->CheckIfCurrentOfferIsApplicable($aSpecialOfferDetails, $iQuantityNotEligibleForOffer)) {
                return true;
            }
        }
        return false;
    }

    private function CheckIfCurrentOfferIsApplicable(array $aItemSpecialOffers, int $iQuantity): bool {
        return (($aItemSpecialOffers["Type"] == self::QUANTITY_BASED && $aItemSpecialOffers["SpecialCondition"] <= $iQuantity) || ($aItemSpecialOffers["Type"] == self::PURCHASED_WITH));
    }
}