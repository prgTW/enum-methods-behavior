<?php

namespace prgTW\Propel\Behavior\EnumMethodsBehavior;

use Propel\Generator\Model\Behavior;

class EnumMethodsBehavior extends Behavior
{
    protected $parameters = array(
        'methodFormat'             => 'is{CamelCasedColumnName}{CamelCasedValue}',
        'useTypeHints'             => 'no',
        'useEnumConstantsBehavior' => 'no',
    );

    /** @var EnumMethodsObjectBuilderModifier */
    private $objectBuilderModifier;

    /** {@inheritdoc} */
    public function getObjectBuilderModifier()
    {
        if (null === $this->objectBuilderModifier) {
            $this->objectBuilderModifier = new EnumMethodsObjectBuilderModifier($this);
        }

        return $this->objectBuilderModifier;
    }
}
