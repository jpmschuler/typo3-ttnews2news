CREATE TABLE tx_news_domain_model_news (
                                           _migrated tinyint(4) unsigned DEFAULT '0' NOT NULL,
                                           _migrated_uid int(11) unsigned DEFAULT '0' NOT NULL,
                                           _migrated_table varchar(255) DEFAULT '' NOT NULL,
                                           _migrated_twice tinyint(4) unsigned DEFAULT '0' NOT NULL
);
CREATE TABLE sys_category (
                              _migrated tinyint(4) unsigned DEFAULT '0' NOT NULL,
                              _migrated_uid int(11) DEFAULT '0' NOT NULL,
                              _migrated_table varchar(255) DEFAULT '' NOT NULL,
                              _migrated_twice tinyint(4) unsigned DEFAULT '0' NOT NULL
);