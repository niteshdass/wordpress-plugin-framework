<?php
namespace EhxDirectorist\Http\Requests;

class StoreCategoryRequest extends RequestGuard {
    
    /**
     * Define validation rules
     */
    public function rules() {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'branch_id' => 'required|string',
        ];
    }

    /**
     * Define custom validation messages
     */
    public function messages() {
        return [
            'name.required' => esc_html__('Category name is required.', 'restaurant-menu-manage'),
            'name.string' => esc_html__('Category name must be a string.', 'restaurant-menu-manage'),
            'name.max' => esc_html__('Category name must not exceed 255 characters.', 'restaurant-menu-manage'),
            'description.string' => esc_html__('Description must be a string.', 'restaurant-menu-manage'),
            'description.max' => esc_html__('Description must not exceed 500 characters.', 'restaurant-menu-manage'),
            'branch_id.required' => esc_html__('Branch id is required.', 'restaurant-menu-manage'),
            'branch_id.string' => esc_html__('Branch id must be an string.', 'restaurant-menu-manage'),
        ];
    }

    /**
     * Define sanitization rules
     */
    public function sanitize() {
        return [
            'name' => 'sanitize_text_field',
            'description' => 'wp_kses_post',
            'branch_id' => 'sanitize_text_field'
        ];
    }
}
