<?php
namespace EhxDirectorist\Http\Requests;

use WP_REST_Request;

abstract class RequestGuard {
    protected $request;
    protected $errors = [];
    protected $validatedData = [];

    public function __construct(WP_REST_Request $request) {
        $this->request = $request;
        $this->validate();
        $this->fails();
    }

    /**
     * Define validation rules in child classes
     */
    abstract public function rules();

    /**
     * Define validation messages in child classes
     */
    public function messages() {
        return [];
    }

    /**
     * Define sanitization methods in child classes
     */
    public function sanitize() {
        return [];
    }

    /**
     * Validate request based on rules
     */
    protected function validate() {
        $params = $this->request->get_json_params();
        $rules = $this->rules();
        $messages = $this->messages();

        foreach ($rules as $field => $rule) {
            $ruleList = explode('|', $rule);

            foreach ($ruleList as $validation) {
                if ($validation === 'required' && empty($params[$field])) {
                    $this->addError($field, $messages["$field.required"] ?? "$field is required.");
                } elseif ($validation === 'string' && !is_string($params[$field])) {
                    $this->addError($field, $messages["$field.string"] ?? "$field must be a string.");
                } elseif ($validation === 'integer' && !filter_var($params[$field], FILTER_VALIDATE_INT)) {
                    $this->addError($field, $messages["$field.integer"] ?? "$field must be an integer.");
                } elseif (str_starts_with($validation, 'max:')) {
                    $max = (int) str_replace('max:', '', $validation);
                    if (!empty($params[$field]) && strlen($params[$field]) > $max) {
                        $this->addError($field, $messages["$field.max"] ?? "$field must not exceed $max characters.");
                    }
                } elseif (str_starts_with($validation, 'in:')) {
                    $allowedValues = explode(',', str_replace('in:', '', $validation));
                    if (!in_array($params[$field], $allowedValues)) {
                        $this->addError($field, $messages["$field.in"] ?? "$field must be one of: " . implode(', ', $allowedValues));
                    }
                }
            }

            // If no errors, sanitize and store validated data
            if (!isset($this->errors[$field])) {
                $this->validatedData[$field] = $this->sanitizeField($field, $params[$field]);
            }
        }
    }

    /**
     * Add an error message
     */
    protected function addError($field, $message) {
        $this->errors[$field][] = esc_html__($message, 'restaurant-menu-manage');
    }

    /**
     * Sanitize fields using the sanitize method map
     */
    protected function sanitizeField($field, $value) {
        $sanitizeMethods = $this->sanitize();

        if (isset($sanitizeMethods[$field]) && function_exists($sanitizeMethods[$field])) {
            return call_user_func($sanitizeMethods[$field], $value);
        }

        return sanitize_text_field($value); // Default sanitization
    }

    /**
     * Check if validation fails
     */
    public function fails() {
        if(!empty($this->errors)) {
            wp_send_json_error([
                'message' => 'Validation failed',
                'errors' => $this->errors
            ]);
        }
    }

    /**
     * Get validation errors
     */
    public function errors() {
        return $this->errors;
    }

    /**
     * Get validated and sanitized data
     */
    public function validated() {
        return $this->validatedData;
    }
}
