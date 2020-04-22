<?
/*----------------------------------------------------------------------------\
|				FireLion Visual Framework Geometry Functions				  |
/*----------------------------------------------------------------------------/
|																			  |
|	Version: 1																  |
|	Date Modified: 15 August 2019 year										  |
|	Time:	13:49 (Ua)														  |
|	Autors:																	  |
|																	Lev Zenin |
|																			  |
\*----------------------------------------------------------------------------/
|
|
*/
///////////////////////////////////////////////////////////////////////////////
///                            Sample functions                             ///
///					Use it for creating Geometry Shapes 					///
///////////////////////////////////////////////////////////////////////////////
function rect($left,$top,$right,$bottom)
{
    return new TRect($left,$top,$right,$bottom);
}

function rectf($left,$top,$right,$bottom)
{
    return new TRectF($left,$top,$right,$bottom);
}

function point($x,$y)
{
    return new TPoint($x,$y);
}

function pointf($x,$y)
{
    return new TPointF($x,$y);
}
