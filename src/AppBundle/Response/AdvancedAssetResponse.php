<?php

namespace AppBundle\Response;

use AppBundle\Entity\Asset;

class AdvancedAssetResponse extends AssetResponse
{
  protected function encodeAsset(Asset $asset)
  {
    return [
      'id'       => $asset->getId(),
      'title'    => $asset->getTitle(),
      'turnover' => $asset->getTurnover(),
      'staff'    => $asset->getStaff(),
      'address'  => $asset->getAddress(),
      'lat'      => $asset->getLat(),
      'long'     => $asset->getLong(),
    ];
  }
}
