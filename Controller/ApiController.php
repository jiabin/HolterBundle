<?php

namespace Jiabin\HolterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends Controller
{
    /**
     * Runs all checks
     */
    public function checkAction()
    {
        $cf   = $this->get('holter.check_factory');
        $data = array();

        $globalStatus  = 0;
        $lastCheckedAt = null;

        foreach ($cf->getChecks() as $name => $check) {
            $result = $cf->getResult($name);
            $data[$name] = $result->toArray();
            $data[$name]['label'] = $check->getLabel(); 

            // Global status
            if ($result->getStatus() > $globalStatus) {
                $globalStatus = $result->getStatus();
            }
            // Global last checked
            if (is_null($lastCheckedAt) or $result->getCreatedAt() > $lastCheckedAt) {
                $lastCheckedAt = $result->getCreatedAt();
            }
        }

        // Global
        $data['global'] = array(
            'status'     => $globalStatus,
            'created_at' => $lastCheckedAt ?: new \DateTime(),
        );

        return JsonResponse::create($data, 200);
    }
}