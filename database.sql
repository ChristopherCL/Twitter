CREATE TABLE Users  (
                    id INT(11) AUTO_INCREMENT,
                    userName VARCHAR(100),
                    userHashPassword VARCHAR(255),
                    userEmail VARCHAR(60) UNIQUE,
                    PRIMARY KEY(id)
                    );

CREATE TABLE Tweets  (
                    id INT AUTO_INCREMENT,
                    userId INT(11),
                    textOfTweet VARCHAR(255),
                    tweetCreationDate VARCHAR(60),
                    PRIMARY KEY(id),
                    FOREIGN KEY(userId) REFERENCES Users(id)
                    ON DELETE CASCADE
                    );

CREATE TABLE Comments  (
                    id INT(11) AUTO_INCREMENT,
                    userId INT(11),
                    postId INT(11),
                    textOfComment VARCHAR(255),
                    PRIMARY KEY(id),
                    FOREIGN KEY(userId) REFERENCES Users(id) ON DELETE CASCADE,
                    FOREIGN KEY(postId) REFERENCES Tweets(id) ON DELETE CASCADE
                    );

CREATE TABLE Messages  (
                    id INT(11) AUTO_INCREMENT,
                    senderId INT(11),
                    receiverId INT(11),
                    status INT(11),
                    textOfMessage VARCHAR(255),
                    PRIMARY KEY(id),
                    FOREIGN KEY(senderId) REFERENCES Users(id) ON DELETE CASCADE,
                    FOREIGN KEY(receiverId) REFERENCES Users(id) ON DELETE CASCADE
                    );
