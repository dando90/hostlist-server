CREATE TABLE IF NOT EXISTS hostlist_hosts (
  id INT UNSIGNED NOT NULL PRIMARY KEY,
  fullname VARCHAR(180) NOT NULL,
  company VARCHAR(180) NOT NULL,
  country VARCHAR(80) NOT NULL,
  city VARCHAR(100) NOT NULL,
  primary_domain VARCHAR(120) NOT NULL,
  secondary_domain VARCHAR(160) NOT NULL,
  primary_country VARCHAR(80) NOT NULL,
  secondary_country VARCHAR(80) NOT NULL,
  languages VARCHAR(180) NOT NULL,
  KEY idx_hostlist_location (country, city),
  KEY idx_hostlist_domains (primary_domain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS hostlist_host_details (
  host_id INT UNSIGNED NOT NULL PRIMARY KEY,
  host_experience_years DECIMAL(3,1) NOT NULL,
  team_size SMALLINT UNSIGNED NOT NULL,
  motivation TEXT NOT NULL,
  specific_skills TEXT NOT NULL,
  offer_extra_info TEXT NOT NULL,
  business_impact JSON NOT NULL,
  CONSTRAINT fk_hostlist_details_host FOREIGN KEY (host_id)
    REFERENCES hostlist_hosts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
