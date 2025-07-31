const { Client, LocalAuth, MessageMedia } = require("whatsapp-web.js");
const { phoneNumberFormatter } = require("./formatter");
const express = require("express");
const { body, validationResult } = require("express-validator");
const { response } = require("express");
const fileUpload = require("express-fileupload");
const axios = require("axios");
const mime = require("mime-types");
const app = express();
const port = 8100;

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(
  fileUpload({
    debug: false,
  })
);

// Enhanced Puppeteer configuration for production servers
const puppeteerConfig = {
  headless: true,
  args: [
    '--no-sandbox',
    '--disable-setuid-sandbox',
    '--disable-dev-shm-usage',
    '--disable-accelerated-2d-canvas',
    '--no-first-run',
    '--no-zygote',
    '--single-process', // Important for server environments
    '--disable-gpu',
    '--disable-background-timer-throttling',
    '--disable-backgrounding-occluded-windows',
    '--disable-renderer-backgrounding',
    '--disable-features=TranslateUI',
    '--disable-ipc-flooding-protection',
    '--disable-extensions',
    '--disable-default-apps',
    '--disable-sync',
    '--disable-translate',
    '--hide-scrollbars',
    '--mute-audio',
    '--no-default-browser-check',
    '--no-pings',
    '--disable-web-security',
    '--disable-features=VizDisplayCompositor',
    '--memory-pressure-off',
    '--max_old_space_size=4096'
  ],
  ignoreHTTPSErrors: true,
  ignoreDefaultArgs: ['--disable-extensions'],
  timeout: 60000,
  protocolTimeout: 60000
};

// Add Chrome executable path if available
if (process.env.PUPPETEER_EXECUTABLE_PATH) {
  puppeteerConfig.executablePath = process.env.PUPPETEER_EXECUTABLE_PATH;
} else if (require('fs').existsSync('/usr/bin/google-chrome-stable')) {
  puppeteerConfig.executablePath = '/usr/bin/google-chrome-stable';
} else if (require('fs').existsSync('/usr/bin/chromium-browser')) {
  puppeteerConfig.executablePath = '/usr/bin/chromium-browser';
}

const client = new Client({
  restartOnAuthFail: true,
  puppeteer: puppeteerConfig,
  authStrategy: new LocalAuth({
    dataPath: './.wwebjs_auth',
    clientId: 'wa-gateway-production'
  }),
  webVersionCache: {
    type: 'remote',
    remotePath: 'https://raw.githubusercontent.com/wppconnect-team/wa-version/main/html/2.2412.54.html',
  },
  takeoverOnConflict: true,
  takeoverTimeoutMs: 60000
});

// Add Puppeteer event logging
client.pupPage?.on?.('console', msg => {
  console.log('Browser console:', msg.text());
});

client.pupPage?.on?.('error', err => {
  console.error('Browser error:', err);
});

client.pupPage?.on?.('pageerror', err => {
  console.error('Page error:', err);
});

let cqrCode = "not ready";
let lAuth = false;

client.on("qr", (qr) => {
  cqrCode = qr;
  console.log("QR RECEIVED-> ", qr);
  console.log("QR Code is now ready!");
  console.log('QR Code length:', qr.length);
});

client.on("loading_screen", (percent, message) => {
  console.log('LOADING SCREEN', percent, message);
});

client.on("authenticated", () => {
  console.log('AUTHENTICATED');
  lAuth = true;
});

client.on("auth_failure", (msg) => {
  // Fired if session restore was unsuccessful
  console.error('AUTHENTICATION FAILURE', msg);
  lAuth = false;
});

client.on("ready", () => {
  cqrCode = "WA Gate is ready";
  console.log('READY - WhatsApp client is ready!');
  console.log('Client info:', client.info);
});

client.on("message", async (msg) => {
  //console.log('MESSAGE RECEIVED', msg);
  const conten = msg.body;

  if (conten === "ping") {
    client.sendMessage(msg.from, "Whatsapp ping (RSPON)");
  } else if (conten === "ping reply") {
    msg.reply("Whatsapp ping reply (RSPON)");
  } else if (conten === "!this group info") {
    let chat = await msg.getChat();
    if (chat.isGroup) {
      msg.reply(`
            *Group Details*
            Name: ${chat.name}
            ID : ${chat.id._serialized}
            Description: ${chat.description}
            Created At: ${chat.createdAt.toString()}
            Created By: ${chat.owner.user}
            Participant count: ${chat.participants.length}
        `);
    } else {
      msg.reply("for in Group Only!");
    }
  } else if (conten === "!reaction") {
    msg.react("👍");
  } else if (conten === "!all_groups_info") {
    client.getChats().then((chats) => {
      const groups = chats.filter((chat) => chat.isGroup);

      if (groups.length == 0) {
        msg.reply("You have no group yet.");
      } else {
        let replyMsg = "*THE GROUPS*\n\n";
        groups.forEach((group, i) => {
          replyMsg += `ID: ${group.id._serialized}\nName: ${group.name}\n\n`;
        });
        replyMsg += "use the group id to send a message to the group";
        msg.reply(replyMsg);
      }
    });
  }
  // else {
  //   msg.reply("Hi there " + msg.from);
  // }
});

client.on("disconnected", (reason) => {
  console.log("Client was logged out", reason);
  // Don't exit on disconnect, allow manual reconnection
  lAuth = false;
  cqrCode = 'not ready';
});

client.on('auth_failure', msg => {
  console.error('Authentication failure:', msg);
  // Don't exit on auth failure, allow retry
});

client.on('loading_screen', (percent, message) => {
  console.log('Loading screen:', percent, message);
});

let rejectCalls = true;

client.on("call", async (call) => {
  if (rejectCalls) await call.reject();
  await client.sendMessage(
    call.from,
    `[${call.fromMe ? "Outgoing" : "Incoming"}] Phone call from ${call.from}, type ${call.isGroup ? "group" : ""} ${call.isVideo ? "video" : "audio"} call. ${rejectCalls ? "Please do not call/message this number!" : ""}`
  );
});

/*
client.on('message', async msg => {
    if(msg.hasMedia) {
        const media = await msg.downloadMedia();
        // do something with the media data here
    }
});

client.on('message_create', (msg) => {
    // Fired on all message creations, including your own
    if (msg.fromMe) {
        // do stuff here
    }
});
 
const media = new MessageMedia('image/png', base64Image);
client.sendMessage(media);

const media = MessageMedia.fromFilePath('./path/to/image.png');
client.sendMessage(media);
 
const media = await MessageMedia.fromUrl('https://via.placeholder.com/350x150.png');
client.sendMessage(media);
 
*/

console.log('Starting WhatsApp client initialization...');

// Enhanced process error handlers
process.on('uncaughtException', (err) => {
  console.error('Uncaught Exception:', err);
  // Don't exit on uncaught exceptions in production
  if (process.env.NODE_ENV !== 'production') {
    process.exit(1);
  }
});

process.on('unhandledRejection', (reason, promise) => {
  console.error('Unhandled Rejection at:', promise, 'reason:', reason);
  // Don't exit on unhandled rejections in production
});

// Enhanced client initialization with retry mechanism
let initializationAttempts = 0;
const maxInitializationAttempts = 3;
const initializationDelay = 10000; // 10 seconds

async function initializeClientWithRetry() {
  initializationAttempts++;
  console.log(`Attempt ${initializationAttempts}/${maxInitializationAttempts} - About to call client.initialize()...`);
  
  try {
    await client.initialize();
    console.log('✅ WhatsApp client initialization successful!');
    initializationAttempts = 0; // Reset counter on success
  } catch (err) {
    console.error(`❌ Failed to initialize WhatsApp client (attempt ${initializationAttempts}):`, err.message);
    
    if (err.message.includes('Execution context was destroyed') || 
        err.message.includes('Protocol error')) {
      console.log('🔄 Detected Puppeteer context error, will retry with fresh session...');
      
      // Clean up any existing browser processes
      try {
        if (client.pupBrowser) {
          await client.pupBrowser.close();
        }
      } catch (cleanupErr) {
        console.log('Browser cleanup error (ignored):', cleanupErr.message);
      }
      
      // Clear session data for fresh start
      const fs = require('fs');
      const path = require('path');
      try {
        const authPath = path.join(__dirname, '.wwebjs_auth');
        if (fs.existsSync(authPath)) {
          fs.rmSync(authPath, { recursive: true, force: true });
          console.log('🧹 Cleared WhatsApp session data');
        }
      } catch (cleanupErr) {
        console.log('Session cleanup error (ignored):', cleanupErr.message);
      }
    }
    
    if (initializationAttempts < maxInitializationAttempts) {
      console.log(`⏳ Retrying in ${initializationDelay/1000} seconds...`);
      setTimeout(() => {
        initializeClientWithRetry();
      }, initializationDelay);
    } else {
      console.error('❌ Max initialization attempts reached. Client will remain uninitialized.');
      console.log('💡 You can try to restart the client via /restart-client endpoint');
      // Reset counter for future manual retries
      initializationAttempts = 0;
    }
  }
}

// Start the initialization process
initializeClientWithRetry();

// Add timeout check for QR generation
setTimeout(() => {
  if (cqrCode === 'not ready') {
    console.log('QR Code not generated after 60 seconds. Checking client state...');
    console.log('Client state - lAuth:', lAuth, 'cqrCode:', cqrCode);
  }
}, 60000);

app.get("/", (req, res) => {
  //res.send("WAG API by Mr. Lee");
  res.status(200).json({
    status: true,
    message: "WAG API by Mr. Lee",
  });
});

app.post("/", (req, res) => {
  //res.send("WAG API by Mr Lee");
  res.status(200).json({
    status: true,
    message: "WAG API by Mr Lee",
  });
});

app.get("/uptime", (req, res) => {
  //res.send('WhatApp GATEWAY API')
  res.status(200).json({
    status: true,
    message: "WhatApp GATEWAY uptime " + process.uptime(),
  });
});

app.post("/StopWAG", (req, res) => {
  if (process.uptime() > 30) {
    res.status(200).json({
      status: true,
      message: "Signal to Stop Nodejs (Nodejs uptime : " + process.uptime() + "), WAG now STOP",
    });

    gracefulShutdown();
  } else {
    res.status(422).json({
      status: false,
      message: "Wait till 30s, upTime " + process.uptime(),
    });
  }
});

// Enhanced endpoint to restart WhatsApp client
app.get('/restart-client', async (req, res) => {
  try {
    console.log('🔄 Manual restart requested via API...');
    
    // Reset initialization attempts for fresh start
    initializationAttempts = 0;
    
    if (client) {
      try {
        await client.destroy();
        console.log('✅ Old client destroyed successfully');
      } catch (destroyErr) {
        console.log('⚠️ Error destroying old client (continuing anyway):', destroyErr.message);
      }
    }
    
    // Clear session data
    const fs = require('fs');
    const path = require('path');
    try {
      const authPath = path.join(__dirname, '.wwebjs_auth');
      if (fs.existsSync(authPath)) {
        fs.rmSync(authPath, { recursive: true, force: true });
        console.log('🧹 Session data cleared for fresh start');
      }
    } catch (cleanupErr) {
      console.log('⚠️ Session cleanup error (ignored):', cleanupErr.message);
    }
    
    // Start initialization with retry mechanism
    setTimeout(() => {
      initializeClientWithRetry();
    }, 2000); // Small delay to ensure cleanup is complete
    
    res.json({ 
      success: true, 
      message: 'WhatsApp client restart initiated with retry mechanism',
      details: {
        session_cleared: true,
        retry_enabled: true,
        max_attempts: maxInitializationAttempts
      }
    });
    
  } catch (error) {
    console.error('❌ Error during manual restart:', error);
    res.status(500).json({ 
      success: false, 
      message: 'Failed to restart client: ' + error.message 
    });
  }
});

// New endpoint for troubleshooting information
app.get('/troubleshoot', (req, res) => {
  const fs = require('fs');
  const path = require('path');
  
  const troubleshootInfo = {
    server_info: {
      node_version: process.version,
      platform: process.platform,
      arch: process.arch,
      uptime: process.uptime(),
      memory_usage: process.memoryUsage(),
      environment: process.env.NODE_ENV || 'development'
    },
    whatsapp_status: {
      authenticated: lAuth,
      qr_code_status: cqrCode,
      initialization_attempts: initializationAttempts,
      max_attempts: maxInitializationAttempts
    },
    puppeteer_config: {
      executable_path: puppeteerConfig.executablePath || 'default',
      args_count: puppeteerConfig.args.length,
      headless: puppeteerConfig.headless
    },
    file_system: {
      auth_folder_exists: fs.existsSync(path.join(__dirname, '.wwebjs_auth')),
      cache_folder_exists: fs.existsSync(path.join(__dirname, '.wwebjs_cache')),
      log_file_exists: fs.existsSync(path.join(__dirname, 'wa-gateway.log'))
    },
    common_solutions: [
      'Try restarting the client: GET /restart-client',
      'Check server resources: memory and CPU usage',
      'Ensure Chrome/Chromium is properly installed',
      'Clear browser cache and session data',
      'Check network connectivity to WhatsApp servers',
      'Verify /dev/shm has sufficient space (512MB recommended)'
    ]
  };
  
  res.json(troubleshootInfo);
});

// Enhanced status endpoint
app.get('/status', (req, res) => {
  res.json({
    server: 'running',
    whatsapp_authenticated: lAuth,
    qr_code_status: cqrCode,
    client_state: client ? client.info : 'not_initialized',
    uptime: process.uptime(),
    memory_usage: process.memoryUsage(),
    timestamp: new Date().toISOString()
  });
});

app.post("/WA-QrCode", (req, res) => {
  if (!lAuth) {
    if (cqrCode === "not ready") {
      res.status(422).json({
        status: false,
        message: "QR Not Ready",
        qrBarCode: cqrCode,
      });
    } else {
      res.status(200).json({
        status: true,
        message: "QR Ready",
        qrBarCode: cqrCode,
      });
    }
  } else {
    res.status(200).json({
      status: true,
      message: "Already Login",
      qrBarCode: cqrCode,
    });
  }
});

const checkRegisteredNumber = async function (number) {
  const isRegistered = await client.isRegisteredUser(number);
  return isRegistered;
};

// Send message
app.post("/send-message", [body("number").notEmpty(), body("message").notEmpty()], async (req, res) => {
  const errors = validationResult(req).formatWith(({ msg }) => {
    return msg;
  });

  if (!errors.isEmpty()) {
    return res.status(422).json({
      status: false,
      message: errors.mapped(),
    });
  }

  const number = phoneNumberFormatter(req.body.number);
  const message = req.body.message;

  const isRegisteredNumber = await checkRegisteredNumber(number);

  if (!isRegisteredNumber) {
    return res.status(422).json({
      status: false,
      message: "Bukan nomor WA",
    });
  }

  client
    .sendMessage(number, message)
    .then((response) => {
      res.status(200).json({
        status: true,
        message: "Sukses",
        response: response,
      });
    })
    .catch((err) => {
      res.status(500).json({
        status: false,
        message: "Gagal Kirim",
        response: err,
      });
    });
});

// Send url File
app.post("/send-fileurl", [body("number").notEmpty(), body("fileurl").notEmpty()], async (req, res) => {
  const errors = validationResult(req).formatWith(({ msg }) => {
    return msg;
  });

  if (!errors.isEmpty()) {
    return res.status(422).json({
      status: false,
      message: errors.mapped(),
    });
  }

  const caption = req.body.caption;
  const number = phoneNumberFormatter(req.body.number);
  const isRegisteredNumber = await checkRegisteredNumber(number);

  if (!isRegisteredNumber) {
    return res.status(422).json({
      status: false,
      message: "Bukan nomor WA",
    });
  }

  const cfile = req.body.fileurl;
  const media = await MessageMedia.fromUrl(cfile);

  client
    .sendMessage(number, media, {
      caption: caption,
    })
    .then((response) => {
      res.status(200).json({
        status: true,
        message: "Sukses",
        response: response,
      });
    })
    .catch((err) => {
      res.status(500).json({
        status: false,
        message: "Gagal Kirim",
        response: err,
      });
    });
});

// Send media
app.post("/send-file", [body("number").notEmpty(), body("namafile").notEmpty()], async (req, res) => {
  const errors = validationResult(req).formatWith(({ msg }) => {
    return msg;
  });

  if (!errors.isEmpty()) {
    return res.status(422).json({
      status: false,
      message: errors.mapped(),
    });
  }

  const caption = req.body.caption;
  const number = phoneNumberFormatter(req.body.number);
  const isRegisteredNumber = await checkRegisteredNumber(number);

  if (!isRegisteredNumber) {
    return res.status(422).json({
      status: false,
      message: "Bukan nomor WA",
    });
  }

  const cfile = req.body.namafile;
  const media = await MessageMedia.fromFilePath("media/" + cfile);

  client
    .sendMessage(number, media, {
      caption: caption,
    })
    .then((response) => {
      res.status(200).json({
        status: true,
        message: "Sukses",
        response: response,
      });
    })
    .catch((err) => {
      res.status(500).json({
        status: false,
        message: "Gagal Kirim",
        response: err,
      });
    });
});

const findGroupByName = async function (name) {
  const group = await client.getChats().then((chats) => {
    return chats.find((chat) => chat.isGroup && chat.name.toLowerCase() == name.toLowerCase());
  });
  return group;
};

// Send message to group
// You can use chatID or group name, yea!
app.post(
  "/send-group",
  [
    body("id").custom((value, { req }) => {
      if (!value && !req.body.name) {
        throw new Error("Invalid value, you can use `id` or `name`");
      }
      return true;
    }),
    body("message").notEmpty(),
  ],
  async (req, res) => {
    const errors = validationResult(req).formatWith(({ msg }) => {
      return msg;
    });

    if (!errors.isEmpty()) {
      return res.status(422).json({
        status: false,
        message: errors.mapped(),
      });
    }

    let chatId = req.body.id;
    const groupName = req.body.name;
    const message = req.body.message;

    // Find the group by name
    if (!chatId) {
      const group = await findGroupByName(groupName);
      if (!group) {
        return res.status(422).json({
          status: false,
          message: "No group found with name: " + groupName,
        });
      }
      chatId = group.id._serialized;
    }

    client
      .sendMessage(chatId, message)
      .then((response) => {
        res.status(200).json({
          status: true,
          message: "Sukses",
          response: response,
        });
      })
      .catch((err) => {
        res.status(500).json({
          status: false,
          message: "Gagal Kirim",
          response: err,
        });
      });
  }
);

const server = app.listen(port, () => {
  console.log(`WAG listening on port ${port}`);
});

// Handle process termination gracefully
let isShuttingDown = false;

process.on('SIGINT', () => {
    if (isShuttingDown) return;
    isShuttingDown = true;
    console.log('Received SIGINT. Graceful shutdown...');
    gracefulShutdown();
});

process.on('SIGTERM', () => {
    if (isShuttingDown) return;
    isShuttingDown = true;
    console.log('Received SIGTERM. Graceful shutdown...');
    gracefulShutdown();
});

process.on('uncaughtException', (error) => {
    console.error('Uncaught Exception:', error);
    if (!isShuttingDown) {
        gracefulShutdown();
    }
});

process.on('unhandledRejection', (reason, promise) => {
    console.error('Unhandled Rejection at:', promise, 'reason:', reason);
    // Don't exit on unhandled rejection, just log it
});

function gracefulShutdown() {
    console.log('Starting graceful shutdown...');
    
    // Close HTTP server first
    if (server) {
        server.close(() => {
            console.log('HTTP server closed.');
            
            // Then destroy WhatsApp client
            if (client) {
                client.destroy().then(() => {
                    console.log('WhatsApp client destroyed.');
                    process.exit(0);
                }).catch((err) => {
                    console.error('Error destroying client:', err);
                    process.exit(1);
                });
            } else {
                process.exit(0);
            }
        });
    } else {
        if (client) {
            client.destroy().then(() => {
                console.log('WhatsApp client destroyed.');
                process.exit(0);
            }).catch((err) => {
                console.error('Error destroying client:', err);
                process.exit(1);
            });
        } else {
            process.exit(0);
        }
    }
}

process.on("exit", (code) => {
  console.log(`Nodejs exit with code ${code}`);
});
