/*
 * Prototypephp - 24/08/2017
 * Script de creation des tables de base
 * deux schemas sont necessaires, l'un pour les donnees proprement dites, 
 * l'autre pour la gestion des habilitations et des traces.
 * Le script se deroule en deux parties :
 * - creation du schema pour les donnees
 * - creation du schema pour la gestion des droits
 * modifiez si necessaire le nom des schemas :
 * - lignes 18 et 19 pour le schema des donnees
 * - lignes 41 et 42 pour le schema de gestion des droits
 */

/*
 * Creation du schema contenant les donnees
 */

create schema if not exists data;
set search_path = data;

CREATE SEQUENCE "dbversion_dbversion_id_seq";

CREATE TABLE "dbversion" (
                "dbversion_id" INTEGER NOT NULL DEFAULT nextval('"dbversion_dbversion_id_seq"'),
                "dbversion_number" VARCHAR NOT NULL,
                "dbversion_date" TIMESTAMP NOT NULL,
                CONSTRAINT "dbversion_pk" PRIMARY KEY ("dbversion_id")
);
COMMENT ON TABLE "dbversion" IS 'Table des versions de la base de donnees';
COMMENT ON COLUMN "dbversion"."dbversion_number" IS 'Numero de la version';
COMMENT ON COLUMN "dbversion"."dbversion_date" IS 'Date de la version';


ALTER SEQUENCE "dbversion_dbversion_id_seq" OWNED BY "dbversion"."dbversion_id";

insert into dbversion(dbversion_number, dbversion_date) values ('0.1', '2017-09-01');
/*
 * Creation de la table de saisie des parametres locaux aux donnees
 */
CREATE TABLE "dbparam" (
                "dbparam_id" INTEGER NOT NULL,
                "dbparam_name" VARCHAR NOT NULL,
                "dbparam_value" VARCHAR,
                CONSTRAINT "dbparam_pk" PRIMARY KEY ("dbparam_id")
);
COMMENT ON TABLE "dbparam" IS 'Table des parametres associes de maniere intrinseque a l''instance';
COMMENT ON COLUMN "dbparam"."dbparam_name" IS 'Nom du parametre';
COMMENT ON COLUMN "dbparam"."dbparam_value" IS 'Valeur du paramètre';

insert into dbparam(dbparam_id, dbparam_name) values (1, 'APPLI_title');

/*
 * Creation du schema de gestion des droits
 */
create schema if not exists gacl;
set search_path = gacl;

CREATE TABLE aclacl (
                aclaco_id INTEGER NOT NULL,
                aclgroup_id INTEGER NOT NULL,
                CONSTRAINT aclacl_pk PRIMARY KEY (aclaco_id, aclgroup_id)
);
COMMENT ON TABLE aclacl IS 'Table des droits attribués';


CREATE SEQUENCE aclaco_aclaco_id_seq;

CREATE TABLE aclaco (
                aclaco_id INTEGER NOT NULL DEFAULT nextval('aclaco_aclaco_id_seq'),
                aclappli_id INTEGER NOT NULL,
                aco VARCHAR NOT NULL,
                CONSTRAINT aclaco_pk PRIMARY KEY (aclaco_id)
);
COMMENT ON TABLE aclaco IS 'Table des droits gérés';


ALTER SEQUENCE aclaco_aclaco_id_seq OWNED BY aclaco.aclaco_id;

CREATE SEQUENCE aclappli_aclappli_id_seq;

CREATE TABLE aclappli (
                aclappli_id INTEGER NOT NULL DEFAULT nextval('aclappli_aclappli_id_seq'),
                appli VARCHAR NOT NULL,
                applidetail VARCHAR,
                CONSTRAINT aclappli_pk PRIMARY KEY (aclappli_id)
);
COMMENT ON TABLE aclappli IS 'Table des applications gérées';
COMMENT ON COLUMN aclappli.appli IS 'Nom de l''application pour la gestion des droits';
COMMENT ON COLUMN aclappli.applidetail IS 'Description de l''application';


ALTER SEQUENCE aclappli_aclappli_id_seq OWNED BY aclappli.aclappli_id;

CREATE SEQUENCE aclgroup_aclgroup_id_seq;

CREATE TABLE aclgroup (
                aclgroup_id INTEGER NOT NULL DEFAULT nextval('aclgroup_aclgroup_id_seq'),
                groupe VARCHAR NOT NULL,
                aclgroup_id_parent INTEGER,
                CONSTRAINT aclgroup_pk PRIMARY KEY (aclgroup_id)
);
COMMENT ON TABLE aclgroup IS 'Groupes des logins';


ALTER SEQUENCE aclgroup_aclgroup_id_seq OWNED BY aclgroup.aclgroup_id;

CREATE SEQUENCE acllogin_acllogin_id_seq;

CREATE TABLE acllogin (
                acllogin_id INTEGER NOT NULL DEFAULT nextval('acllogin_acllogin_id_seq'),
                login VARCHAR NOT NULL,
                logindetail VARCHAR NOT NULL,
                CONSTRAINT acllogin_pk PRIMARY KEY (acllogin_id)
);
COMMENT ON TABLE acllogin IS 'Table des logins des utilisateurs autorisés';
COMMENT ON COLUMN acllogin.logindetail IS 'Nom affiché';


ALTER SEQUENCE acllogin_acllogin_id_seq OWNED BY acllogin.acllogin_id;

CREATE TABLE acllogingroup (
                acllogin_id INTEGER NOT NULL,
                aclgroup_id INTEGER NOT NULL,
                CONSTRAINT acllogingroup_pk PRIMARY KEY (acllogin_id, aclgroup_id)
);
COMMENT ON TABLE acllogingroup IS 'Table des relations entre les logins et les groupes';


ALTER TABLE aclacl ADD CONSTRAINT aclaco_aclacl_fk
FOREIGN KEY (aclaco_id)
REFERENCES aclaco (aclaco_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE aclaco ADD CONSTRAINT aclappli_aclaco_fk
FOREIGN KEY (aclappli_id)
REFERENCES aclappli (aclappli_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE aclacl ADD CONSTRAINT aclgroup_aclacl_fk
FOREIGN KEY (aclgroup_id)
REFERENCES aclgroup (aclgroup_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE aclgroup ADD CONSTRAINT aclgroup_aclgroup_fk
FOREIGN KEY (aclgroup_id_parent)
REFERENCES aclgroup (aclgroup_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE acllogingroup ADD CONSTRAINT aclgroup_acllogingroup_fk
FOREIGN KEY (aclgroup_id)
REFERENCES aclgroup (aclgroup_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE acllogingroup ADD CONSTRAINT acllogin_acllogingroup_fk
FOREIGN KEY (acllogin_id)
REFERENCES acllogin (acllogin_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

insert into aclappli (aclappli_id, appli) values (1, 'appli');
insert into aclaco (aclaco_id, aclappli_id, aco) values (1, 1, 'admin');
insert into acllogin (acllogin_id, login, logindetail) values (1, 'admin', 'admin');
insert into aclgroup (aclgroup_id, groupe) values (1, 'admin');
insert into acllogingroup (acllogin_id, aclgroup_id) values (1, 1);
insert into aclacl (aclaco_id, aclgroup_id) values (1, 1);

CREATE TABLE logingestion (
    id integer NOT NULL,
    login character varying(32) NOT NULL,
    password character varying(255),
    nom character varying(32),
    prenom character varying(32),
    mail character varying(255),
    datemodif date,
    actif smallint default 1
);
ALTER TABLE  logingestion
    ADD CONSTRAINT pk_logingestion PRIMARY KEY (id);
	
CREATE SEQUENCE seq_logingestion_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 999999
    CACHE 1;

ALTER TABLE logingestion ALTER COLUMN id SET DEFAULT nextval('seq_logingestion_id'::regclass);

insert into logingestion (id, login, password, nom) values (1, 'admin', 'cd916028a2d8a1b901e831246dd5b9b4d3832786ddc63bbf5af4b50d9fc98f50', 'Administrator');

CREATE SEQUENCE login_oldpassword_login_oldpassword_id_seq;

CREATE TABLE login_oldpassword (
                login_oldpassword_id INTEGER NOT NULL DEFAULT nextval('login_oldpassword_login_oldpassword_id_seq'),
                id INTEGER DEFAULT nextval('seq_logingestion_id'::regclass) NOT NULL,
                password VARCHAR(255),
                CONSTRAINT login_oldpassword_pk PRIMARY KEY (login_oldpassword_id)
);
COMMENT ON TABLE login_oldpassword IS 'Table contenant les anciens mots de passe';


ALTER SEQUENCE login_oldpassword_login_oldpassword_id_seq OWNED BY login_oldpassword.login_oldpassword_id;

ALTER TABLE login_oldpassword ADD CONSTRAINT logingestion_login_oldpassword_fk
FOREIGN KEY (id)
REFERENCES logingestion (id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

CREATE SEQUENCE log_log_id_seq;

CREATE TABLE log (
                log_id INTEGER NOT NULL DEFAULT nextval('log_log_id_seq'),
                login VARCHAR(32) NOT NULL,
                nom_module VARCHAR,
                log_date TIMESTAMP NOT NULL,
                commentaire VARCHAR,
                ipaddress varchar,
                CONSTRAINT log_pk PRIMARY KEY (log_id)
);
COMMENT ON TABLE log IS 'Liste des connexions ou des actions enregistrées';
COMMENT ON COLUMN log.log_date IS 'Heure de connexion';
COMMENT ON COLUMN log.commentaire IS 'Donnees complementaires enregistrees';
comment on column log.ipaddress is 'Adresse IP du client';

ALTER SEQUENCE log_log_id_seq OWNED BY log.log_id;

CREATE INDEX log_date_idx
 ON log
 ( log_date );

CREATE INDEX log_login_idx
 ON log
 ( login );

 CREATE SEQUENCE "passwordlost_passwordlost_id_seq";

CREATE TABLE "passwordlost" (
                "passwordlost_id" INTEGER NOT NULL DEFAULT nextval('"passwordlost_passwordlost_id_seq"'),
                "id" INTEGER NOT NULL,
                "token" VARCHAR NOT NULL,
                "expiration" TIMESTAMP NOT NULL,
                "usedate" TIMESTAMP,
                CONSTRAINT "passwordlost_pk" PRIMARY KEY ("passwordlost_id")
);
COMMENT ON TABLE "passwordlost" IS 'Table de suivi des pertes de mots de passe';
COMMENT ON COLUMN "passwordlost"."token" IS 'Jeton utilise pour le renouvellement';
COMMENT ON COLUMN "passwordlost"."expiration" IS 'Date d''expiration du jeton';


ALTER SEQUENCE "passwordlost_passwordlost_id_seq" OWNED BY "passwordlost"."passwordlost_id";

ALTER TABLE "passwordlost" ADD CONSTRAINT "logingestion_passwordlost_fk"
FOREIGN KEY ("id")
REFERENCES "logingestion" ("id")
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;
 
/*
 * ajout des autres droits necessaires dans l'application
 */
insert into aclaco (aclaco_id, aclappli_id, aco) 
values 
(2, 1, 'param'),
(3, 1, 'gestion'),
(4, 1, 'consult');
insert into aclgroup (aclgroup_id, groupe, aclgroup_id_parent) 
values 
(2, 'consult', null),
(3, 'gestion', 2),
(4, 'param', 3);

insert into aclacl (aclaco_id, aclgroup_id)
values 
(2, 4),
(3, 3),
(4, 2);

select setval('seq_logingestion_id', (select max(id) from logingestion));
select setval('aclappli_aclappli_id_seq', (select max(aclappli_id) from aclappli));
select setval('aclaco_aclaco_id_seq', (select max(aclaco_id) from aclaco));
select setval('acllogin_acllogin_id_seq', (select max(acllogin_id) from acllogin));
select setval('aclgroup_aclgroup_id_seq', (select max(aclgroup_id) from aclgroup));
