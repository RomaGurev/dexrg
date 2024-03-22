/*Файл для инициализации новой базы данных*/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
        
CREATE TABLE `conscript` (
  `id` int(11) NOT NULL,
  `creatorID` int(11) NOT NULL,
  `creationDate` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `birthDate` varchar(100) NOT NULL,
  `rvkArticle` varchar(10) NOT NULL,
  `vk` varchar(255) NOT NULL,
  `healthCategory` varchar(100) NOT NULL,
  `adventPeriod` varchar(50) NOT NULL,
  `inProcess` boolean DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        
ALTER TABLE `conscript`
    ADD PRIMARY KEY (`id`);
        
ALTER TABLE `conscript`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;


CREATE TABLE `changeCategory` (
  `id` int(11) NOT NULL,
  `conscriptID` int(11) NOT NULL,
  `article` varchar(10) NOT NULL,
  `healthCategory` varchar(100) NOT NULL,
  `creatorID` int(11) NOT NULL,
  `complaint` varchar(1000) NOT NULL,
  `anamnez` varchar(1000) NOT NULL,
  `objectData` varchar(1500) NOT NULL,
  `specialResult` varchar(1500) NOT NULL,
  `diagnosis` varchar(1500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `changeCategory`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `changeCategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
