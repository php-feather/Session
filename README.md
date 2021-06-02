# Session

##### DB Session Handler Table SCHEMA

```
CREATE TABLE sessions (
    id varchar(65) CHARACTER SET ascii NOT NULL DEFAULT '',
    sess_data text,
    expire_at int unsigned DEFAULT NULL,
    PRIMARY KEY (id),
    KEY sess_expire_at_dx (expire_at)
);
```
