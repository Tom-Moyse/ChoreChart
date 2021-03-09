# ChoreChart

### Features
- User can login to and register for accounts using an email, username and password
- A user can create a new chore group with a given name
- A user can join a chore group using the group 'join code' which is automatically generated  
for any given group
    - This makes it very easy for a group of people who want to use the application to all  
    join the same group
- A moderator is able to perform all the actions of a normal user in addition to moderator  
only actions such as:
    - Promoting/removing other users moderator status
    - Accepting/declining group join requests
    - Creating, editing and removing chores
- Some of the actions all users can perform are:
    - Updating any details: display name, password, email
    - Viewing a table of group chores with all relevant information available
    - Viewing a table of their individual chores with similar information available
    - Viewing their 'active chore', which is the most urgent chore they have yet to complete
    - Leaving a given group such that they can join a different group
- Their are two main types of chores that being one-off chores and repeating chores
    - For any chore the moderator can set the description/title, deadline, choreholder
    - For any repeating chore the moderator can set both a start date and repeating interval
    - All chores fields can be edited wherein it makes sense (e.g. can't edit start date for   
    chore that has passed)
    - All chores can be manually assigned to a given user, or set to 'auto choreholder'  
    meaning it will be fairly assigned automatically.

### Additional Features
- Seperate moderator and standard status.
    - This helps the potential user groups remain organised with regards to who allocates and  
    manages the households chores. In addition, it allows the join request sytem to work  
    effectively with only certain group members being able to accept new group members.  
- Join Request system.
    - The join request sytem in place not only allows multiple users to be able to join any  
    given group with a low degree of effort (only requiring a 5 digit alphanumeric code),   
    but also enables a level of privacy to any given group with no arbitrary user being able  
    to participate in the group unless directly accepted by a moderator. This means even if  
    an unfriendly user managed to guess the join code they would likely still not be accepted   
    into the group.
- Email notifications
    - Linked into the join request system are email notifications wherein upon a user  
    requesting to join a group an email is sent to all group moderators informing them of  
    this status so they are aware of the request and able to respond. Similarly, when the  
    request is accepted/declined an email is sent to the original requester such that they  
    are aware of this fact.
- Profile picture
    - A user is able to easily upload a profile picture to their account that is subsequently  
    stored on the server and can be accessed as and when required. This helps users of the  
    app to be easily identified/identify  others within the group.

### Image sources
- https://depositphotos.com/352791508/stock-illustration-cleaning-schedule-pixel-perfect-linear.html?utm_medium=affiliate&utm_source=widget&utm_campaign=sp.depositphotos.com&utm_term=4&utm_content=33083
- https://commons.wikimedia.org/wiki/File:Magnifying_glass_icon.svg
- https://media.flaticon.com/dist/assets/crown.b2abc241d531de58992ce1805cfbe66f.svg
- https://iconarchive.com/show/silky-line-user-icons-by-custom-icon-design/user-icon.html
