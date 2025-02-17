CREATE TABLE `motorcycle_types`
(
    `id`   INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `motorcycles`
(
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `name`         VARCHAR(255) NOT NULL,
    `discontinued` TINYINT(1) NOT NULL DEFAULT 0,
    `type_id`      INT,
    FOREIGN KEY (`type_id`) REFERENCES `motorcycle_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `motorcycle_types` (`name`)
VALUES ('Sport'),
       ('Cruiser'),
       ('Touring'),
       ('Naked');

INSERT INTO `motorcycles` (`name`, `discontinued`, `type_id`)
VALUES ('Yamaha-R1', 0, 1),
       ('CBR1000RR', 1, 1),
       ('Gold Wing', 0, 3),
       ('Harley Softail', 0, 2),
       ('Ninja ZX-10R', 0, 1),
       ('Vulcan 900', 1, 2);