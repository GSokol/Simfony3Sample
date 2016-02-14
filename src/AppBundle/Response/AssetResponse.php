<?php

namespace AppBundle\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Entity\Asset;

abstract class AssetResponse extends JsonResponse
{
  public function setAsset(Asset $asset)
  {
    $this->setData($this->encodeAsset($asset));

    return $this;
  }

  public function setAssetPlural(array $assets)
  {
    $this->setData(array_map(
      function ($x) { return $this->encodeAsset($x); },
      $assets
    ));

    return $this;
  }

  protected abstract function encodeAsset(Asset $asset);
}
