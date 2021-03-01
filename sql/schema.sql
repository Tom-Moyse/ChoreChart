DROP TABLE ChoreGroup;
DROP TABLE JoinRequest;
DROP TABLE User;
DROP TABLE Chore;
DROP TABLE ChoreItem;

CREATE TABLE ChoreGroup(
    ID INTEGER PRIMARY KEY,
    code VARCHAR(5),
    gname TEXT
);

CREATE TABLE JoinRequest(
    ID INTEGER PRIMARY KEY,
    GroupID INTEGER,
    UserID INTEGER,
    FOREIGN KEY(GroupID) REFERENCES ChoreGroup(ID),
    FOREIGN KEY(UserID) REFERENCES User(ID)
);

CREATE TABLE User(
  ID INTEGER PRIMARY KEY, 
  email VARCHAR(50),
  username VARCHAR(30),
  displayname VARCHAR(30),
  pass PASSWORD VARCHAR(255),
  moderator BOOLEAN,
  GroupID INTEGER,
  FOREIGN KEY(GroupID) REFERENCES ChoreGroup(ID)
);

CREATE TABLE Chore(
    ID INTEGER PRIMARY KEY,
    contents TEXT,
    repeats BOOLEAN,
    interval TEXT,
    fixed BOOLEAN,
    startdate DATETIME,
    lastchoreitemdate DATETIME,
    GroupID INTEGER,
    UserID INTEGER,
    FOREIGN KEY(UserID) REFERENCES User(ID),
    FOREIGN KEY(GroupID) REFERENCES ChoreGroup(ID)
);

CREATE TABLE ChoreItem(
    ID INTEGER PRIMARY KEY,
    contents TEXT,
    completed BOOLEAN,
    deadline DATETIME,
    ChoreID INTEGER,
    UserID INTEGER,
    FOREIGN KEY(ChoreID) REFERENCES Chore(ID),
    FOREIGN KEY(UserID) REFERENCES User(ID)
);