<?php
/**
 * Validation Helper
 * Provides consistent input validation across all endpoints
 */

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Check if a field is required
     */
    public function required(string $field, string $message = null): self
    {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = $message ?? "Feld '$field' ist erforderlich";
        }
        return $this;
    }

    /**
     * Check if multiple fields are required
     */
    public function requiredFields(array $fields): self
    {
        foreach ($fields as $field) {
            $this->required($field);
        }
        return $this;
    }

    /**
     * Validate email format
     */
    public function email(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? 'Ungültige E-Mail-Adresse';
        }
        return $this;
    }

    /**
     * Validate minimum length
     */
    public function minLength(string $field, int $length, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? "Feld '$field' muss mindestens $length Zeichen lang sein";
        }
        return $this;
    }

    /**
     * Validate maximum length
     */
    public function maxLength(string $field, int $length, string $message = null): self
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = $message ?? "Feld '$field' darf maximal $length Zeichen lang sein";
        }
        return $this;
    }

    /**
     * Validate numeric value
     */
    public function numeric(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = $message ?? "Feld '$field' muss eine Zahl sein";
        }
        return $this;
    }

    /**
     * Validate positive number
     */
    public function positive(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && is_numeric($this->data[$field]) && floatval($this->data[$field]) <= 0) {
            $this->errors[$field] = $message ?? "Feld '$field' muss größer als 0 sein";
        }
        return $this;
    }

    /**
     * Validate value is in a list of allowed values
     */
    public function inArray(string $field, array $allowed, string $message = null): self
    {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $allowed)) {
            $this->errors[$field] = $message ?? "Ungültiger Wert für '$field'";
        }
        return $this;
    }

    /**
     * Validate integer
     */
    public function integer(string $field, string $message = null): self
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
            $this->errors[$field] = $message ?? "Feld '$field' muss eine ganze Zahl sein";
        }
        return $this;
    }

    /**
     * Custom validation with callback
     */
    public function custom(string $field, callable $callback, string $message): self
    {
        if (isset($this->data[$field]) && !$callback($this->data[$field])) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    /**
     * Check if validation passed
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error message
     */
    public function getFirstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }

    /**
     * Get a sanitized value
     */
    public function getValue(string $field, $default = null)
    {
        return isset($this->data[$field]) ? trim($this->data[$field]) : $default;
    }

    /**
     * Get sanitized integer value
     */
    public function getInt(string $field, int $default = 0): int
    {
        return isset($this->data[$field]) ? intval($this->data[$field]) : $default;
    }

    /**
     * Get sanitized float value
     */
    public function getFloat(string $field, float $default = 0.0): float
    {
        return isset($this->data[$field]) ? floatval($this->data[$field]) : $default;
    }

    /**
     * Static factory method
     */
    public static function make(array $data): self
    {
        return new self($data);
    }
}
