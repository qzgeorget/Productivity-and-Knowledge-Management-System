// refill the search with placeholder if empty
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');

    searchInput.addEventListener('input', function() {
        if (searchInput.value === '') {
            searchInput.placeholder = '\uD83D\uDD0D Enter for search...'; // Using Unicode escape sequence
        }
    }); 

     // Create contacts
    const contactsContainer = document.createElement('div');
    contactsContainer.classList.add('contacts-container');
    const contactsDiv = document.createElement('div');
    contactsDiv.classList.add('contacts');
    const contactName = document.createElement('h5');
    contactName.classList.add('contact');
    contactName.textContent = '';
    const deleteButton = document.createElement('button');
    deleteButton.classList.add('button');
    deleteButton.id = 'delete-button';
    deleteButton.innerHTML = '<img src="delete.svg" alt="Delete Chat">';
    contactsDiv.appendChild(contactName);
    contactsDiv.appendChild(deleteButton);
    contactsContainer.appendChild(contactsDiv);
    document.getElementById('menu').appendChild(contactsContainer);

    // Create chat container
    const chatContainer = document.getElementById('chat');
    // Create contact name container
    const contactNameContainer = document.createElement('div');
    contactNameContainer.id = 'contact-name-container';

    // Create contact name element
    const contactNameTitle = document.createElement('h2');
    contactNameTitle.id = 'contact-name-title';
    contactNameTitle.textContent = '';

    // Append contact name to contact name container
    contactNameContainer.appendChild(contactName);

    // Append contact name container to chat container
    chatContainer.appendChild(contactNameContainer);

    // Create messages container
    const messagesContainer = document.createElement('div');
    messagesContainer.classList.add('messages-container');

    // Create sent messages div
    const sentMessages = document.createElement('div');
    sentMessages.classList.add('sent-messages');
    sentMessages.innerHTML = '<p>efefv</p>';

    // Create received messages div
    const receivedMessages = document.createElement('div');
    receivedMessages.classList.add('received-messages');
    receivedMessages.innerHTML = '<p>ddef</p>';

    // Append sent and received messages to messages container
    messagesContainer.appendChild(sentMessages);
    messagesContainer.appendChild(receivedMessages);

    // Append messages container to chat container
    chatContainer.appendChild(messagesContainer);
    
    // Create message input textarea
    const messageInput = document.createElement('textarea');
    messageInput.id = 'message-input';
    messageInput.placeholder = 'Type your message...';
    
    // Create send button
    const sendButton = document.createElement('button');
    sendButton.id = 'send-button';
    sendButton.textContent = 'Send';
    sendButton.addEventListener('click', sendMessage); // Assuming sendMessage is a function defined elsewhere

    // Append message input textarea and send button to chat container
    chatContainer.appendChild(messageInput);
    chatContainer.appendChild(sendButton);

    // Append chat container to the document body or any other parent element you desire
    document.body.appendChild(chatContainer);

    document.addEventListener('DOMContentLoaded', function() {
        // get the div elements
        const contacts = document.querySelector('.contacts');
        const chatsDiv = document.querySelector('#chat');
        const menuDiv = document.querySelector('#menu');

        let count = 0;

        // add a click event listener to the div
        contacts.addEventListener('click', function() {
            // specify the action to take when the div is clicked
            if (count === 0) {
                console.log('Div was clicked!');
                menuDiv.style.width = '17vw';
                chatsDiv.style.display = 'block';
                count++;
            } else {
                chatsDiv.style.display = 'none';
                menuDiv.style.width = '94vw';
                count = 0;
            }
        });
    }); 

        function sendMessage() {
        console.log('hey');
    }

    fetch('index.php')
        .then(response => {
            // Check if the response is successful (status code 200-299)
            if (!response.ok) {
            throw new Error('Network response was not ok');
            }
            
            // Parse the response as JSON
            return response.json();
        })
        .then(data => {
            // Handle the data from the response
            console.log(data);
        })
        .catch(error => {
            // Handle any errors that occur during the fetch request
            console.error('Fetch error:', error);
        });

}); 
