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
  `rvkDiagnosis` varchar(2500) NOT NULL,
  `vk` varchar(255) NOT NULL,
  `healthCategory` varchar(100) NOT NULL,
  `adventPeriod` varchar(50) NOT NULL,
  `postPeriod` varchar(11) NOT NULL,
  `rvkProtocolDate` varchar(100) NOT NULL,
  `rvkProtocolNumber` varchar(100) NOT NULL,
  `protocolDate` varchar(100) NOT NULL,
  `protocolNumber` varchar(100) NOT NULL,
  `letterNumber` varchar(100) NOT NULL,
  `inProcess` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `conscriptID` int(11) NOT NULL,
  `article` varchar(10) NOT NULL,
  `healthCategory` varchar(100) NOT NULL,
  `creatorID` int(11) NOT NULL,
  `complaint` varchar(2500) NOT NULL,
  `anamnez` varchar(2500) NOT NULL,
  `objectData` varchar(2500) NOT NULL,
  `specialResult` varchar(2500) NOT NULL,
  `diagnosis` varchar(2500) NOT NULL,
  `postPeriod` varchar(11) NOT NULL,
  `documentDate` varchar(100) NOT NULL,
  `documentType` varchar(100) NOT NULL,
  `reasonForCancel` varchar(2500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `conscript`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `conscript`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;