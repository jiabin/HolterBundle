<?php

namespace Jiabin\HolterBundle\CheckType;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Form\FormBuilderInterface;

class Linode extends CheckType
{
    /**
     * {@inheritdoc}
     */
    static $name = 'linode';

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $feed = new \SimplePie();
        $feed->set_feed_url('http://status.linode.com/blog/atom.xml');
        $feed->enable_cache(false);
        $feed->init();

        $facility = $this->options->get('facility');
        list($open, $closed) = array(0, 0);
        foreach ($feed->get_items() as $item) {
            $title = $item->get_title();
            if ( $title == sprintf('%s Connectivity Issues', ucfirst($facility)) ) {
                $open++;
            } elseif ( $title == sprintf('RESOLVED - %s Connectivity Issues', ucfirst($facility)) ) {
                $closed++;
            }
        }

        if ($open > $closed) {
            $result = $this->buildResult(sprintf('%s connectivity issues', ucfirst($facility)), Status::MINOR);
        } else {
            $result = $this->buildResult('Connection established', Status::GOOD);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public static function buildOptionsForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('facility', 'choice', array(
                'required' => true,
                'choices'  => array(
                    'atlanta' => 'Atlanta',
                    'dallas'  => 'Dallas',
                    'fremont' => 'Fremont',
                    'london'  => 'London',
                    'newark'  => 'Newark',
                    'tokyo'   => 'Tokyo'
                )
            ))
        ;
    }
}
