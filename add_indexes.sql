-- Add indexes to property table for optimization
CREATE INDEX idx_address ON property(address);
CREATE INDEX idx_price ON property(price);
CREATE INDEX idx_type ON property(type);
CREATE INDEX idx_offer ON property(offer);
CREATE INDEX idx_bhk ON property(bhk);
CREATE INDEX idx_status ON property(status);
CREATE INDEX idx_furnished ON property(furnished);
CREATE INDEX idx_date ON property(date);
CREATE INDEX idx_user_id ON property(user_id);

-- Composite index for common search combinations
CREATE INDEX idx_search_combo ON property(address, type, offer, price);

-- Show all indexes
SHOW INDEX FROM property;
