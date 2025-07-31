const { Client, LocalAuth } = require('whatsapp-web.js');

console.log('Creating simple WhatsApp client...');

const client = new Client({
    authStrategy: new LocalAuth()
});

client.on('qr', (qr) => {
    console.log('QR RECEIVED:', qr);
    console.log('QR Code generated successfully!');
});

client.on('ready', () => {
    console.log('Client is ready!');
});

client.on('auth_failure', msg => {
    console.error('AUTHENTICATION FAILURE', msg);
});

client.on('disconnected', (reason) => {
    console.log('Client was logged out', reason);
});

console.log('Initializing client...');
client.initialize().then(() => {
    console.log('Initialize promise resolved');
}).catch(err => {
    console.error('Initialize failed:', err);
});

console.log('Script started, waiting for events...');