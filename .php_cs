<?php

$header = <<<'EOF'
@see       https://github.com/soluble-io/soluble-mediatools for the canonical repository
@copyright Copyright (c) 2018-2019 Sébastien Vanvelthem. (https://github.com/belgattitude)
@license   https://github.com/soluble-io/soluble-mediatools/blob/master/LICENSE.md MIT
EOF;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        // enable when you want to add header
        //'header_comment' => array('header' => $header, 'comment_type' => 'PHPDoc'),
        '@PSR2' => true,
        '@PHP71Migration' => true,
        'psr4' => true,
        'array_syntax' => ['syntax' => 'short'],

        'strict_comparison' => true,
        'strict_param' => true,

        'dir_constant' => true,
        'pow_to_exponentiation' => true,
        'is_null' => true,

        'no_homoglyph_names' => true,
        'no_null_property_initialization' => true,
        'no_php4_constructor' => true,
        'non_printable_character' => true,
        'ordered_imports' => true,

        'align_multiline_comment' => true,

        /**
         * Extended code rules
         */
        'binary_operator_spaces' => [
            // 'align_single_space', 'align_single_space_minimal', 'single_space', null): default fix strategy; defaults to 'single_space'
            'default' =>  'single_space',
            'operators' =>
                [
                    '=>' => 'align_single_space',
                    '=' => 'align_single_space_minimal'
                ]
        ],

        'blank_line_after_opening_tag' => true,
        'blank_line_before_return' => true,
        'cast_spaces' => true,
        'class_definition' => ['singleLine' => true],
        'concat_space' => ['spacing' => 'one'], // Different from symfony (none)
        'declare_equal_normalize' => true,
        'function_typehint_space' => true,
        'hash_to_slash_comment' => true,
        'heredoc_to_nowdoc' => true,
        'include' => true,
        'lowercase_cast' => true,
        'mb_str_functions' => true,
        'method_separation' => true,
        'native_function_casing' => true,
        'new_with_braces' => true,
        'no_alias_functions' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_consecutive_blank_lines' => [
            'curly_brace_block',
            'extra',
            'parenthesis_brace_block',
            'square_brace_block',
            'throw',
            'use',
        ],
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => ['use' => 'echo'],
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_short_bool_cast' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => true,
        'no_trailing_comma_in_list_call' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unneeded_control_parentheses' => true,
        'no_unreachable_default_argument_value' => true,
        'no_unused_imports' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'normalize_index_brace' => true,
        'object_operator_without_whitespace' => true,
        'php_unit_fqcn_annotation' => true,
        'phpdoc_align' => true,
        'phpdoc_annotation_without_dot' => true,
        'phpdoc_indent' => true,
        'phpdoc_inline_tag' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_no_package' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_var_without_name' => false,
        'pre_increment' => true,
        'return_type_declaration' => true,
        'self_accessor' => true,
        'short_scalar_cast' => true,
        'single_blank_line_before_namespace' => true,
        'single_class_element_per_statement' => true,
        'single_quote' => true,
        'space_after_semicolon' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline_array' => false, // Differs from Symfony (true)
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,

        // Taken from @Symfony:risky

        'php_unit_construct' => true,
        'php_unit_dedicate_assert' => true,
        'php_unit_fqcn_annotation' => true,
        'silenced_deprecation_error' => true,
        'declare_strict_types' => true,

    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->name('/\.php|\.php.dist$/')
            ->in(['src', 'tests'])
    )
;

