-- Add is_active column to products table if it doesn't exist
-- This allows archiving products without deleting them
ALTER TABLE products ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE;

-- Update delete_product handler to use soft delete (archive)
-- Products can now be hidden without violating foreign key constraints
