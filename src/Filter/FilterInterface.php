<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminBundle\Filter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
interface FilterInterface
{
    public const CONDITION_OR = 'OR';

    public const CONDITION_AND = 'AND';

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/admin-bundle 3.78, to be removed with 4.0
     *
     * Apply the filter to the ProxyQueryInterface instance.
     *
     * @param string  $alias
     * @param string  $field
     * @param mixed[] $data
     *
     * @phpstan-param array{type?: int|null, value?: mixed} $data
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data);

    /**
     * @param ProxyQueryInterface $query
     * @param mixed[]             $filterData
     *
     * @phpstan-param array{type?: int|null, value?: mixed} $filterData
     */
    public function apply($query, $filterData);

    /**
     * Returns the filter name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the filter form name.
     *
     * @return string
     */
    public function getFormName();

    /**
     * Returns the label name.
     *
     * @return string|bool
     */
    public function getLabel();

    /**
     * @param string $label
     */
    public function setLabel($label);

    /**
     * @return array<string, mixed>
     */
    public function getDefaultOptions();

    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null);

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setOption($name, $value);

    /**
     * @param string $name
     */
    public function initialize($name, array $options = []);

    /**
     * @return string
     */
    public function getFieldName();

    /**
     * @return array<string, string> array of mappings
     */
    public function getParentAssociationMappings();

    /**
     * @return array<string, string> field mapping
     */
    public function getFieldMapping();

    /**
     * @return array<string, string> association mapping
     */
    public function getAssociationMapping();

    /**
     * @return array<string, mixed>
     */
    public function getFieldOptions();

    /**
     * Get field option.
     *
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getFieldOption($name, $default = null);

    /**
     * Set field option.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setFieldOption($name, $value);

    /**
     * @return string
     */
    public function getFieldType();

    /**
     * Returns the main widget used to render the filter.
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    public function getRenderSettings();

    /**
     * Returns true if filter is active.
     *
     * @return bool
     */
    public function isActive();

    /**
     * Set the condition to use with the left side of the query : OR or AND.
     *
     * @param string $condition
     */
    public function setCondition($condition);

    /**
     * @return string
     */
    public function getCondition();

    /**
     * @return string
     */
    public function getTranslationDomain();
}
