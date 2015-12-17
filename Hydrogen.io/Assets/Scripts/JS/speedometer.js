function draw(iSpeed) {
  var canvas = document.getElementById('tutorial');

  if (canvas != null && canvas.getContext) {
    var options = buildOptionsAsJSON(canvas, iSpeed);

    clearCanvas(options);

    drawMetallicArc(options);

    drawBackground(options);

    drawTicks(options);

    drawTextMarkers(options);

    drawSpeedometerColourArc(options);

    drawNeedle(options);
  } else {
    alert("Canvas not suppoerted by your browser!");
  }

  function drawMetallicArc(options) {
    drawOuterMetalicArc(options);
    drawInnerMetalicArc(options);
  }

  function drawOuterMetalicArc(options) {
    options.ctx.beginPath();
    options.ctx.fillStyle = "rgb(127,127,127)";
    options.ctx.arc(options.center.X, options.center.Y,options.radius,0,Math.PI,true);
    options.ctx.fill();
  }

  function drawInnerMetalicArc(options) {
    options.ctx.beginPath();
    options.ctx.fillStyle = "rgb(255,255,255)";
    options.ctx.arc(options.center.X,options.center.Y,(options.radius / 100) * 90, 0, Math.PI, true);
    options.ctx.fill();
  }
}

function drawBackground(options) {
  options.ctx.globalAlpha = 0.2;
  options.ctx.fillStyle = "rgb(0,0,0)";
  for (var i = 170; i < 180; i++) {
    options.ctx.beginPath();
    options.ctx.arc(options.center.X, options.center.Y, 1 * i, 0, Math.PI,true);
    options.ctx.fill();
  }
}

function drawTicks(options) {
  drawSmallTickMarks(options);
  drawLargeTickMarks(options);
}

function drawSmallTickMarks(options) {
  var tickValue = options.levelRadius - 8;
  var iTick = 0;
  var gaugeOptions = options.gaugeOptions;
  var iTickRad = 0;

  applyDefaultContextSettings(options);

  for (iTick = 10; iTick < 180; iTick += 20) {
    iTickRad = degToRad(iTick);

    var onArchX = gaugeOptions.radius - (Math.cos(iTickRad) * tickValue);
    var onArchY = gaugeOptions.radius - (Math.sin(iTickRad) * tickValue);
    var innerTickX = gaugeOptions.radius - (Math.cos(iTickRad) * gaugeOptions.radius);
    var innerTickY = gaugeOptions.radius - (Math.sin(iTickRad) * gaugeOptions.radius);

    var fromX = (options.center.X - gaugeOptions.radius) + onArchX;
    var fromY = (gaugeOptions.center.Y - gaugeOptions.radius) + onArchY;

    var toX = (options.center.X - gaugeOptions.radius) + innerTickX;
    var toY = (gaugeOptions.center.Y - gaugeOptions.radius) + innerTickY;

    var line = createLine(fromX, fromY, toX, toY, "rgb(127,127,127)", 3, 0.6);
    drawLine(options,line);
  }
}

function drawLargeTickMarks(options) {
  var tickvalue = options.levelRadius - 8;
    var iTick = 0;
    var gaugeOptions = options.gaugeOptions;
    var iTickRad = 0;

    var innerTickY;
    var innerTickX;
    var onArchX;
    var onArchY;

    var fromX;
    var fromY;

    var toX;
    var toY;
    var line;

    applyDefaultContextSettings(options);

    tickvalue = options.levelRadius - 2;

    for (iTick = 20; iTick <18; iTick +=20) {
      iTickRad = degToRad(iTick);

      onArchX = gaugeOptions.radius - (Math.cos(iTickRad) * tickvalue);
      onArchY = gaugeOptions.radius - (Math.sin(iTickRad) * tickvalue);
      innerTickX = gaugeOptions.radius - (Math.cos(iTickRad) * gaugeOptions.radius);
      innerTickY = gaugeOptions.radius - (Math.sin(iTickRad) * gaugeOptions.radius);

      fromX = (options.center.X - gaugeOptions.radius) + onArchX;
      fromY = (gaugeOptions.center.Y - gaugeOptions.radius) + onArchY;

      toX = (optins.center.X - gaugeOptions.radius) + innerTickX;
      toY = (gaugeOptions.center.Y - gaugeOptions.radius) + innerTickY;

      line = createLine(fromX, fromY, toX, toY, "rgb(127,127,127)",3,0.6);
      drawLine(options,line);
    }
}

function drawTextMarkers(options) {
  var innerTickX = 0;
  var innerTickY = 0;
  var iTick = 0;
  var gaugeOptions = options.gaugeOptions;
  var iTickToPrint = 0;

  applyDefaultContextSettings(options);

  options.ctx.font = 'italic 10px sans-serif';
  options.ctx.textBaseline = 'top';

  options.ctx.beginPath();

  for (iTick = 10; iTick < 180; iTick += 20) {
    innerTickX = gaugeOptions.radius - (Math.cos(degToRad(iTick)) * gaugeOptions.radius);
    innerTickY = gaugeOptions.radius - (Math.sin(degToRad(iTick)) * gaugeOptions.radius);

    if (iTick < 50) {
      options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius -12) + innerTickX - 5, (gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY + 5);
    }
    else if(iTick < 90)
    {
      options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX,(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY );
    }
    else if(iTick == 90)
    {
      options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX + 4,(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY );
    }
    else if(iTick < 145)
    {
      options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX + 10,(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY );
    }
    else
    {
      options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX + 15,(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY + 5);
    }

    iTickToPrint += 10;
  }
  options.ctx.stroke();
}

function drawSpeedometerColourArc(options) {
  var startOfGreen = 10;
  var endOfGreen = 200;
  var endOfOrange = 280;

  drawSpeedometerPart(options, 1.0, "rgb(82,240,55)", startOfGreen);
  drawSpeedometerPart(options, 0.9, "rgb(198,111,0)",endOfGreen);
  drawSpeedometerPart(options, 0.9, "rgb(255,0,0)",endOfOrange)
}

function drawNeedleDial(options, alphaValue, strokeStyle, fillStyle) {
  options.ctx.globalAlpha = alphaValue;
  options.ctx.lineWidth = 3;
  options.ctx.strokeStyle = strokeStyle;
  options.ctx.fillStyle = fillStyle;

  for (var i = 0; i < 30; i++) {
    options.ctx.beginPath();
    options.ctx.arc(options.center.X, options.center.Y, 1*i, 0, Math.PI, true);
    options.ctx.fill();
    options.ctx.stroke();
  }
}

function drawNeedle(options) {
  var iSpeedAsAngle = convertSpeedToAngle(options);
  var iSpeedAsAngleRad = degToRad(iSpeedAsAngle);

  var gaugeOptions = options.gaugeOptions;

  var innerTickX = gaugeOptions.radius - (Math.cos(iSpeedAsAngleRad) * 20);
  var innerTickY = gaugeOptions.radius - (Math.sin(iSpeedAsAngleRad) * 20);

  var fromX = (options.center.X - gaugeOptions.radius) + innerTickX;
  var fromY = (gaugeOptions.center.Y - gaugeOptions.radius) + innerTickY;

  var endNeedleX = gaugeOptions.radius - (Math.cos(iSpeedAsAngleRad) * gaugeOptions.radius);
  var endNeedleY = gaugeOptions.radius - (Math.sin(iSpeedAsAngleRad) * gaugeOptions.radius);

  var toX = (options.center.X - gaugeOptions.radius) + endNeedleX;
  var toY = (gaugeOptions.center.Y - gaugeOptions.radius) + endNeedleY;

  var line = createLine(fromX, fromY, toX, toY, "rgb(255,0,0)", 5, 0.6);

  drawLine(options, line);

  drawNeedleDial(options, 0.6, "rgb(127,127,127)","rgb(255,255,255)");
  drawNeedleDial(options, 0.2, "rgb(127,127,127)","rgb(127,127,127)");
}

function buildOptionsAsJSON(canvas, iSpeed) {
	var centerX = 210,
	    centerY = 210,
        radius = 140,
        outerRadius = 200;
  return {
		ctx: canvas.getContext('2d'),
		speed: iSpeed,
		center:	{
			X: centerX,
			Y: centerY
		},
		levelRadius: radius - 10,
		gaugeOptions: {
			center:	{
				X: centerX,
				Y: centerY
			},
			radius: radius
		},
		radius: outerRadius
	};
}

function applyDefaultContextSettings(options) {
	/* Helper function to revert to gauges
	 * default settings
	 */

	options.ctx.lineWidth = 2;
	options.ctx.globalAlpha = 0.5;
	options.ctx.strokeStyle = "rgb(255, 255, 255)";
	options.ctx.fillStyle = 'rgb(255,255,255)';
}

function degToRad(angle) {
	// Degrees to radians
	return ((angle * Math.PI) / 180);
}

function createLine(fromX, fromY, toX, toY, fillStyle, lineWidth, alpha) {
	// Create a line object using Javascript object notation
	return {
		from: {
			X: fromX,
			Y: fromY
		},
		to:	{
			X: toX,
			Y: toY
		},
		fillStyle: fillStyle,
		lineWidth: lineWidth,
		alpha: alpha
	};
}

function drawLine(options, line) {
	// Draw a line using the line object passed in
	options.ctx.beginPath();

	// Set attributes of open
	options.ctx.globalAlpha = line.alpha;
	options.ctx.lineWidth = line.lineWidth;
	options.ctx.fillStyle = line.fillStyle;
	options.ctx.strokeStyle = line.fillStyle;
	options.ctx.moveTo(line.from.X,
		line.from.Y);

	// Plot the line
	options.ctx.lineTo(
		line.to.X,
		line.to.Y
	);

	options.ctx.stroke();
}

function drawSpeedometerPart(options, alphaValue, strokeStyle, startPos) {
	/* Draw part of the arc that represents
	* the colour speedometer arc
	*/

	options.ctx.beginPath();

	options.ctx.globalAlpha = alphaValue;
	options.ctx.lineWidth = 5;
	options.ctx.strokeStyle = strokeStyle;

	options.ctx.arc(options.center.X,
		options.center.Y,
		options.levelRadius,
		Math.PI + (Math.PI / 360 * startPos),
		0 - (Math.PI / 360 * 10),
		false);

	options.ctx.stroke();
}

function convertSpeedToAngle(options) {
	/* Helper function to convert a speed to the
	* equivelant angle.
	*/
	var iSpeed = (options.speed / 10),
	    iSpeedAsAngle = ((iSpeed * 20) + 10) % 180;

	// Ensure the angle is within range
	if (iSpeedAsAngle > 180) {
        iSpeedAsAngle = iSpeedAsAngle - 180;
    } else if (iSpeedAsAngle < 0) {
        iSpeedAsAngle = iSpeedAsAngle + 180;
    }

	return iSpeedAsAngle;
}

function clearCanvas(options) {
	options.ctx.clearRect(0, 0, 800, 600);
	applyDefaultContextSettings(options);
}
