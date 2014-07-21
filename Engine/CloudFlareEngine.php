<?php

namespace Jiabin\HolterBundle\Engine;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Form\FormBuilderInterface;

class CloudFlareEngine extends AbstractEngine
{
    /**
     * @var array
     */
    protected $array;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cloudflare';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'CloudFlare';
    }

    /**
     * {@inheritdoc}
     */
    public function check($options)
    {
        // Cache response
        if (empty($this->array)) {
            $url  = 'https://www.cloudflare.com/api/v2/sys_status';
            $json = file_get_contents($url);

            $this->array = json_decode($json, true);
        }

        if (array_key_exists($options['data-center'], $this->array['response']['colos'])) {
            $today = current($this->array['response']['colos'][$options['data-center']]);
            if ($today) {
                switch ($today['status']) {
                    case 'online':
                        $result = $this->buildResult('Running normally.', Status::GOOD);
                        break;
                    case 'degraded':
                        $result = $this->buildResult('Degraded performance.', Status::MINOR);
                        break;
                    case 'maintenance':
                        $result = $this->buildResult('Performing scheduled maintenance.', Status::MAJOR);
                        break;
                    case 'offline':
                    case 'error':
                        $result = $this->buildResult('I see dead people.', Status::MAJOR);
                        break;
                    default:
                        $result = $this->buildResult('Unknown status.', Status::UNKNOWN);
                        break;
                }
                return $result;
            }
        }

        return $this->buildResult('Unknown status.', Status::UNKNOWN);
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('data-center', 'choice', array(
                'required' => true,
                'choices'  => array(
                    'Amsterdam, NL' => 'Amsterdam, NL',
                    'Ashburn, VA' => 'Ashburn, VA',
                    'Atlanta, GA' => 'Atlanta, GA',
                    'Barueri, BR' => 'Barueri, BR',
                    'Chicago, IL' => 'Chicago, IL',
                    'Dallas, TX' => 'Dallas, TX',
                    'Frankfurt, DE' => 'Frankfurt, DE',
                    'Hong Kong, HK' => 'Hong Kong, HK',
                    'London, GB' => 'London, GB',
                    'Los Angeles, CA' => 'Los Angeles, CA',
                    'Madrid, ES' => 'Madrid, ES',
                    'Miami, FL' => 'Miami, FL',
                    'Milan, IT' => 'Milan, IT',
                    'Newark, NJ' => 'Newark, NJ',
                    'Paris, FR' => 'Paris, FR',
                    'Prague, CZ' => 'Prague, CZ',
                    'San Jose, CA' => 'San Jose, CA',
                    'Seattle, WA' => 'Seattle, WA',
                    'Seoul, KR' => 'Seoul, KR',
                    'Singapore, SG' => 'Singapore, SG',
                    'Stockholm, SE' => 'Stockholm, SE',
                    'Sydney, AU' => 'Sydney, AU',
                    'Tokyo, JP' => 'Tokyo, JP',
                    'Toronto, ON' => 'Toronto, ON',
                    'Valparaiso, CL' => 'Valparaiso, CL',
                    'Vienna, AT' => 'Vienna, AT',
                    'Warsaw, PL' => 'Warsaw, PL'
                )
            ))
        ;
    }
}
