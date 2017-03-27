<?php

namespace prgTW\Propel\Behavior\EnumMethodsBehavior;

use Propel\Generator\Builder\Om\ObjectBuilder;

class EnumMethodsObjectBuilderModifier
{
    /** @var EnumMethodsBehavior */
    private $behavior;

    public function __construct(EnumMethodsBehavior $behavior)
    {
        $this->behavior = $behavior;
    }

    public function objectMethods(ObjectBuilder $builder)
    {
        $methodFormat             = $this->behavior->getParameter('methodFormat');
        $useTypeHints             = 'yes' === $this->behavior->getParameter('useTypeHints');
        $useEnumConstantsBehavior = 'yes' === $this->behavior->getParameter('useEnumConstantsBehavior');

        $script = '';
        foreach ($builder->getTable()->getColumns() as $column) {
            $values = $column->getValueSet();
            if (is_array($values) && count($values) && false !== strpos($column->getAttribute('sqltype'), 'enum(')) {
                foreach ($values as $enum) {
                    $enumCamelCased = preg_replace('/[\W_-]/ui', ' ', $enum);
                    $enumCamelCased = str_replace('  ', ' ', $enumCamelCased);
                    $enumCamelCased = str_replace(' ', '', ucwords($enumCamelCased));
                    $script         .= $this->behavior->renderTemplate(
                        'isser',
                        array(
                            'returnTypeHint'       => $useTypeHints ? ': bool' : '',
                            'methodName'           => strtr(
                                $methodFormat,
                                array(
                                    '{CamelCasedColumnName}' => ucfirst($column->getCamelCaseName()),
                                    '{CamelCasedValue}'      => $enumCamelCased,
                                )
                            ),
                            'camelCasedColumnName' => ucfirst($column->getCamelCaseName()),
                            'value'                => $useEnumConstantsBehavior ?
                                vsprintf(
                                    '%s::%s',
                                    array(
                                        $builder->getTableMapClass(),
                                        $column->getConstantName().'_'.preg_replace(
                                            '/[^0-9a-z]/ui',
                                            '_',
                                            strtoupper($enum)
                                        ),
                                    )
                                )
                                :
                                var_export($enum, true),
                        )
                    );
                }
            }
        }

        return $script;
    }
}
