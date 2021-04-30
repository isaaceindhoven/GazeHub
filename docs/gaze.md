# Gaze
Gaze, staring in the distance waiting for something to occur.

Gaze is a framework that can be used for server-to-client communication and uses Server-Sent Events under the hood. Gaze is split up into three different modules: [GazeHub](gazehub.md), [GazeClient](gazeclient.md) and [GazePublisher](gazepublisher.md).

Gaze allows a developer to add realtime-updates to an existing project without having to modify the entire codebase. See [example](example.md) for a detailed example of how to install Gaze.

## What problem does Gaze solve?
The initial problem was that we needed some kind of way to push data from the server to the client, without the client needing to do a request. The modern web offers a lot of protocols to achieve this goal. Unfortunately, there are no plug-and-play solutions to integrate easily into an existing project. This is the problem that Gaze solves.

## How do all the Gaze modules fit together?

| Module | Usage |
| --- | --- |
| [GazeHub](gazehub.md) | This is the center of whole Gaze. The hub allows clients to listen for events and servers to send events to. |
| [GazeClient](gazeclient.md) | This is the frontend library that allows a browser to connect to GazeHub and subscribe to events. |
| [GazePublisher](gazepublisher.md) | This is the backend library that allows an application to send events to GazeHub. |
