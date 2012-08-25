<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Simple Sample Annotated Pimcore CreateJS Template</title>
</head>
<body xmlns:dcterms="http://purl.org/dc/terms/" xmlns:participant="http://pimcore/object/participant/">
<div style="height: 70px;"></div>
<h1>This is simple example using the Pimcore CreateJS plugin</h1>
<p>Just some static text here ...</p>
<div about="@document">
    <h2 property="dcterms:title">sample head</h2>
    <p property="dcterms:content">
        sample content
    </p>
    <h1>Participant</h1>
    <div rel="participants"  rev="participantOf">
        <div about="http://pimcore/object/participant/5" typeof="participant:red">
            <div>Name: <span property="participant:name">sample participant name</span></div>
            <div>Age: <span property="participant:age">12</span></div>
        </div>
    </div>
</div>
</body>
</html>

