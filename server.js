// Основной сервер CAD на Node.js с полной обработкой всех типов сообщений клиента
// Для запуска: npm install ws express

const WebSocket = require('ws');
const express = require('express');
const http = require('http');

const app = express();
const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

// Хранилища данных (в памяти)
let calls = [];
let units = [];
let bolos = [];
let incidents = [];
let rms_reports = [];
let users = [];

// Генерация уникальных ID
function genId(prefix) {
  return prefix + '_' + Math.random().toString(36).substr(2, 9);
}

// Шаблон ответа
function makeResponse(type, dataType, payload, request_id, success = true, error = null) {
  return JSON.stringify({
    type,
    dataType,
    payload,
    request_id,
    success,
    error
  });
}

// WebSocket обработка
wss.on('connection', function(ws) {
  ws.on('message', function(message) {
    let msg;
    try {
      msg = JSON.parse(message);
    } catch (e) {
      ws.send(makeResponse('error', 'parse', null, null, false, 'Invalid JSON'));
      return;
    }

    // Универсальная обработка по типу и action
    switch (msg.type) {
      case 'calls':
        handleCalls(ws, msg);
        break;
      case 'unit':
        handleUnits(ws, msg);
        break;
      case 'bolos':
        handleBolos(ws, msg);
        break;
      case 'incidents':
        handleIncidents(ws, msg);
        break;
      case 'rms':
        handleRMS(ws, msg);
        break;
      case 'auth':
        handleAuth(ws, msg);
        break;
      default:
        ws.send(makeResponse('error', 'unknown_type', null, msg.request_id, false, 'Unknown type'));
    }
  });
});

// --- Handlers ---
function handleCalls(ws, msg) {
  switch (msg.action) {
    case 'get':
      ws.send(makeResponse('data_broadcast', 'calls', calls, msg.request_id));
      break;
    case 'add':
      const call = { ...msg.payload, id: genId('call') };
      calls.push(call);
      broadcastAll('calls', calls);
      ws.send(makeResponse('success', 'add_call', call, msg.request_id));
      break;
    case 'add_note':
      {
        const call = calls.find(c => c.id === msg.payload.call_id);
        if (call) {
          call.notes = call.notes || [];
          call.notes.push(msg.payload.note);
          broadcastAll('calls', calls);
          ws.send(makeResponse('success', 'add_call_note', call, msg.request_id));
        } else {
          ws.send(makeResponse('error', 'add_call_note', null, msg.request_id, false, 'Call not found'));
        }
      }
      break;
    default:
      ws.send(makeResponse('error', 'calls', null, msg.request_id, false, 'Unknown action'));
  }
}

function handleUnits(ws, msg) {
  switch (msg.action) {
    case 'form_or_update_crew':
      let unit = units.find(u => u.unitID === msg.payload.unitID);
      if (unit) {
        Object.assign(unit, msg.payload);
      } else {
        unit = { ...msg.payload };
        units.push(unit);
      }
      broadcastAll('units', units);
      ws.send(makeResponse('success', 'unit_update', unit, msg.request_id));
      break;
    default:
      ws.send(makeResponse('error', 'unit', null, msg.request_id, false, 'Unknown action'));
  }
}

function handleBolos(ws, msg) {
  switch (msg.action) {
    case 'get':
      ws.send(makeResponse('data_broadcast', 'bolos', bolos, msg.request_id));
      break;
    case 'add':
      const bolo = { ...msg.payload, id: genId('bolo') };
      bolos.push(bolo);
      broadcastAll('bolos', bolos);
      ws.send(makeResponse('success', 'add_bolo', bolo, msg.request_id));
      break;
    case 'add_note':
      {
        const bolo = bolos.find(b => b.id === msg.payload.bolo_id);
        if (bolo) {
          bolo.notes = bolo.notes || [];
          bolo.notes.push(msg.payload.note);
          broadcastAll('bolos', bolos);
          ws.send(makeResponse('success', 'add_bolo_note', bolo, msg.request_id));
        } else {
          ws.send(makeResponse('error', 'add_bolo_note', null, msg.request_id, false, 'BOLO not found'));
        }
      }
      break;
    case 'update_status':
      {
        const bolo = bolos.find(b => b.id === msg.payload.bolo_id);
        if (bolo) {
          bolo.status = msg.payload.status;
          broadcastAll('bolos', bolos);
          ws.send(makeResponse('success', 'update_bolo_status', bolo, msg.request_id));
        } else {
          ws.send(makeResponse('error', 'update_bolo_status', null, msg.request_id, false, 'BOLO not found'));
        }
      }
      break;
    default:
      ws.send(makeResponse('error', 'bolos', null, msg.request_id, false, 'Unknown action'));
  }
}

function handleIncidents(ws, msg) {
  switch (msg.action) {
    case 'get':
      ws.send(makeResponse('data_broadcast', 'incidents', incidents, msg.request_id));
      break;
    case 'add':
      const incident = { ...msg.payload, id: genId('incident') };
      incidents.push(incident);
      broadcastAll('incidents', incidents);
      ws.send(makeResponse('success', 'add_incident', incident, msg.request_id));
      break;
    default:
      ws.send(makeResponse('error', 'incidents', null, msg.request_id, false, 'Unknown action'));
  }
}

function handleRMS(ws, msg) {
  switch (msg.action) {
    case 'get_reports':
      ws.send(makeResponse('data_broadcast', 'rms_reports', rms_reports, msg.request_id));
      break;
    case 'get_report_details':
      const report = rms_reports.find(r => r.recordId === msg.payload.recordId);
      if (report) {
        ws.send(makeResponse('data_broadcast', 'rms_report_details', report, msg.request_id));
      } else {
        ws.send(makeResponse('error', 'rms_report_details', null, msg.request_id, false, 'Report not found'));
      }
      break;
    case 'create_report':
      const newReport = { ...msg.payload, recordId: genId('report') };
      rms_reports.push(newReport);
      broadcastAll('rms_reports', rms_reports);
      ws.send(makeResponse('success', 'create_report', newReport, msg.request_id));
      break;
    default:
      ws.send(makeResponse('error', 'rms', null, msg.request_id, false, 'Unknown action'));
  }
}

function handleAuth(ws, msg) {
  // Примитивная авторизация (заглушка)
  if (msg.action === 'login') {
    const user = users.find(u => u.username === msg.payload.username);
    if (user && user.password === msg.payload.password) {
      ws.send(makeResponse('success', 'auth', { token: 'dummy_token', user }, msg.request_id));
    } else {
      ws.send(makeResponse('error', 'auth', null, msg.request_id, false, 'Invalid credentials'));
    }
  } else {
    ws.send(makeResponse('error', 'auth', null, msg.request_id, false, 'Unknown action'));
  }
}

// Рассылка всем клиентам
function broadcastAll(dataType, payload) {
  wss.clients.forEach(function each(client) {
    if (client.readyState === WebSocket.OPEN) {
      client.send(makeResponse('data_broadcast', dataType, payload));
    }
  });
}

// HTTP-заглушка (можно сделать сайт)
app.get('/', (req, res) => {
  res.send('CAD WebSocket Server is running.');
});

server.listen(8443, () => {
  console.log('CAD WebSocket server running on ws://0.0.0.0:8443');
});
