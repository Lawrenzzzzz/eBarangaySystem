ALTER TABLE budgets
    ADD COLUMN allocation_locked_at DATETIME NULL AFTER total_budget;

UPDATE budgets b
SET b.allocation_locked_at = b.updated_at
WHERE EXISTS (
    SELECT 1 FROM budget_allocations ba WHERE ba.budget_id = b.id
);
