<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use AppBundle\Response\CommonAssetResponse;
use AppBundle\Response\AdvancedAssetResponse;

class AssetController extends Controller
{
  public function getAction($id, Request $request)
  {
    $asset = $this->getDoctrine()
      ->getRepository('AppBundle:Asset')
      ->find($id);

    if (!$asset) {
      throw $this->createNotFoundException(
        'No asset found for id ' . $id
      );
    }

    if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADVANCED')) {
      return (new CommonAssetResponse())->setAsset($asset);
    }
    return (new AdvancedAssetResponse())->setAsset($asset);
  }

  public function findAction(Request $request)
  {
    $logger = $this->get('logger');
    if (
      ($request->request->has('min_turnover') || $request->request->has('max_turnover'))
      && !$this->get('security.authorization_checker')->isGranted('ROLE_ADVANCED')
    ) {
      throw $this->createAccessDeniedException();
    }

    $minTurnover = $request->query->get('min_turnover');
    $maxTurnover = $request->query->get('max_turnover');
    $minStaff = $request->query->get('min_staff');
    $maxStaff = $request->query->get('max_staff');

    $logger->debug("minTurnover=$minTurnover, maxTurnover=$maxTurnover, minStaff=$minStaff, maxStaff=$maxStaff");

    $assets = $this->getDoctrine()
      ->getRepository('AppBundle:Asset')
      ->search($minTurnover, $maxTurnover, $minStaff, $maxStaff);

    if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADVANCED')) {
      return (new CommonAssetResponse())->setAssetPlural($assets);
    }
    return (new AdvancedAssetResponse())->setAssetPlural($assets);
  }

  public function getFilterAction(Request $request)
  {
    $data = [];
    if ($this->get('security.authorization_checker')->isGranted('ROLE_ADVANCED')) {
      $data[] = [
        'name' => 'Оборот',
        'type' => 'float',
        'data_name' => 'turnover',
        'min_value' => 0,
        'max_value' => 10000000,
        'currency' => 'руб.',
      ];
    }

    $data[] = [
      'name' => 'Штат',
      'type' => 'float',
      'data_name' => 'staff',
      'min_value' => 0,
      'max_value' => 10000,
      'currency' => 'человек',
    ];

    $resp = new JsonResponse();
    $resp->setData($data);

    return $resp;
  }
}
