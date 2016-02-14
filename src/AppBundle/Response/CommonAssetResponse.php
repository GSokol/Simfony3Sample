<?php

namespace AppBundle\Response;

use AppBundle\Entity\Asset;

class CommonAssetResponse extends AssetResponse
{
  protected function encodeAsset(Asset $asset)
  {
    return [
      'id'      => $asset->getId(),
      'title'   => $asset->getTitle(),
      'staff'   => $asset->getStaff(),
      'address' => $asset->getAddress(),
      'lat'     => $asset->getLat(),
      'long'    => $asset->getLong(),
    ];
  }
}
