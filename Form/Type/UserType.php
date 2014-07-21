<?php

namespace Jiabin\HolterBundle\Form\Type;

use Jiabin\HolterBundle\Manager\HolterManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserType extends AbstractType
{
    /**
     * @var HolterManager
     */
    protected $manager;

    /**
     * @var RoleHierarchyInterface
     */
    protected $roleHierarchy;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * Class constructor
     *
     * @param HolterManager            $manager
     * @param RoleHierarchyInterface   $roleHierarchy
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(HolterManager $manager, RoleHierarchyInterface $roleHierarchy, SecurityContextInterface $securityContext)
    {
        $this->manager         = $manager;
        $this->roleHierarchy   = $roleHierarchy;
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'help_block' => 'john@doe.com'
            ))
            ->add('name', 'text', array(
                'help_block' => 'John Doe'
            ))
            ->add('plain_password', 'password', array(
                'required' => false
            ))
            ->add('roles', 'choice', array(
                'multiple' => true,
                'choices' => $this->getRoleChoices()
            ))
        ;
    }

    public function getRoleChoices()
    {
        $array = array();
        $roles = $this->roleHierarchy->getReachableRoles($this->securityContext->getToken()->getRoles());
        foreach ($roles as $role) {
            $array[$role->getRole()] = $role->getRole();
        }

        return $array;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'User form',
            'data_class' => $this->manager->userClass
        ));
    }

    public function getName()
    {
        return 'holter_user';
    }

}
